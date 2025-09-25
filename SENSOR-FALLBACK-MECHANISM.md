# Sensor Fallback Mechanism Documentation

## Overview

This document describes the comprehensive sensor fallback mechanism implemented to handle missing or delayed IoT sensor data in the COTA (Water Quality Monitoring) system. The mechanism ensures data continuity for real-time dashboard applications by providing intelligent fallback strategies when sensors fail to send data or when data arrives late.

## Business Context

- **Primary Goal**: Data continuity over accuracy for demo/client presentation
- **Use Case**: Real-time dashboard monitoring of water quality parameters
- **Device**: ESP32-based IoT sensors sending data every 1 minute
- **Parameters Monitored**: Turbidity (kekeruhan), pH (keasaman), Temperature (suhu)

## Architecture Overview

The fallback mechanism consists of three main components:

1. **Database Layer**: Enhanced sensor data storage with quality metadata
2. **Service Layer**: Smart historical analysis and data processing
3. **Job Layer**: Automated missing data detection and fallback generation

## Database Schema Changes

### New Columns Added to `sensors` Table

```sql
ALTER TABLE sensors ADD COLUMN data_source ENUM('REAL_TIME', 'FALLBACK', 'BACKFILLED') DEFAULT 'REAL_TIME';
ALTER TABLE sensors ADD COLUMN is_estimated BOOLEAN DEFAULT FALSE;
ALTER TABLE sensors ADD COLUMN reading_timestamp TIMESTAMP NULL COMMENT 'Actual sensor reading time';
```

### Data Source Types

- **REAL_TIME**: Data received from sensors within expected timeframe
- **FALLBACK**: Generated data when sensors fail to send readings
- **BACKFILLED**: Late-arriving real data inserted after fallback was generated

## Configuration System

### Configuration File: `config/sensor-fallback.php`

```php
return [
    'trigger_delay_minutes' => 2,           // Trigger fallback after 2 minutes
    'expected_interval_minutes' => 1,       // Expected data every 1 minute
    'historical_days' => 7,                 // Look back 7 days for patterns
    'time_window_minutes' => 5,             // ±5 minute window for historical data
    'minimum_historical_days' => 3,         // Minimum 3 days before using historical fallback
    'late_data_threshold_minutes' => 5,     // Threshold for delayed data handling
    'default_values' => [                   // Bootstrap fallback values
        'kekeruhan' => ['min' => 40.0, 'max' => 50.0],
        'keasaman' => ['min' => 6.8, 'max' => 7.2],
        'suhu' => ['min' => 28.0, 'max' => 30.0],
    ],
    'noise_percentage' => 0.05,             // 5% adaptive noise
    'enable_logging' => true,
];
```

## Core Mechanism Workflow

### 1. Missing Data Detection

**Laravel Scheduler** runs every minute to detect missing data:

```php
// app/Console/Kernel.php
$schedule->job(new ProcessMissingSensorData())
         ->everyMinute()
         ->name('process-missing-sensor-data')
         ->withoutOverlapping(2);
```

**Detection Logic**:
- Check for data in the last 2-3 minutes
- Look for gaps in expected 1-minute intervals
- Consider ±30 seconds tolerance for timestamp matching

### 2. Fallback Data Generation

#### Phase 1: Basic Historical Average
- Simple average calculation from same time period over last 7 days
- Fixed noise injection (5%)
- Default values for insufficient historical data

#### Phase 2: Smart Historical Average (Enhanced)

**Priority-Based Time Window Selection**:
1. **Priority 1**: Exact minute matches (±30 seconds)
2. **Priority 2**: ±2 minute window if insufficient exact matches
3. **Priority 3**: ±5 minute window if still insufficient data

**Weighted Calculation**:
```php
$combinedWeight = $timeWeight * $dayWeight * $recencyWeight;

// Where:
// - timeWeight: Higher for closer time matches (1 - timeDiff/60)
// - dayWeight: 1.2x for same day of week, 1.0x otherwise
// - recencyWeight: Higher for more recent data (1 - daysDiff/30)
```

**Adaptive Noise Injection**:
- Calculate historical variance for each parameter
- Apply noise based on variance: `min(variance * 0.3, value * 0.1)`
- More realistic than fixed percentage noise

### 3. Delayed Data Handling

When sensors send data late, the system handles it intelligently:

#### Scenario 1: Minor Delay (< 5 minutes)
```php
// Replace existing fallback data with real data
$existingFallback = Sensor::where('data_source', 'FALLBACK')
    ->where('created_at', '>=', $actualTime->subSeconds(30))
    ->where('created_at', '<=', $actualTime->addSeconds(30))
    ->first();

if ($existingFallback) {
    $existingFallback->update([
        'data_source' => 'REAL_TIME',
        'is_estimated' => false,
        // ... real sensor values
    ]);
}
```

#### Scenario 2: Major Delay (> 5 minutes)
```php
// Insert as backfilled data, keep both records
Sensor::create([
    'data_source' => 'BACKFILLED',
    'is_estimated' => false,
    'reading_timestamp' => $actualReadingTime,
    'created_at' => $actualReadingTime,
    // ... sensor values
]);
```

## API Enhancements

### Enhanced Sensor Data Insertion

**Endpoint**: `POST /api/sensor-data/insert`

**Enhanced Request Body**:
```json
{
    "kekeruhan": 45.2,
    "keasaman": 7.1,
    "suhu": 29.5,
    "reading_timestamp": "2024-01-15T14:23:00Z"  // Optional for delayed data
}
```

**Response Examples**:

Real-time data:
```json
{
    "message": "Data sensor berhasil ditambahkan!",
    "status": 201,
    "data": { /* sensor resource */ }
}
```

Delayed data:
```json
{
    "message": "Data sensor delay berhasil diproses!",
    "status": 201,
    "delay_minutes": 3,
    "handling_strategy": "replaced_fallback"
}
```

### New Data Quality Endpoint

**Endpoint**: `GET /api/sensor-data/quality`

**Optional Parameters**:
- `start_date`: Filter from date (YYYY-MM-DD)
- `end_date`: Filter to date (YYYY-MM-DD)

**Response**:
```json
{
    "message": "Data quality statistics retrieved successfully.",
    "status": 200,
    "data": {
        "total_records": 1440,
        "real_time_percentage": 92.5,
        "fallback_percentage": 6.8,
        "backfilled_percentage": 0.7,
        "data_quality_score": 93.2
    },
    "period": {
        "start_date": "2024-01-01",
        "end_date": "2024-01-31"
    }
}
```

## Service Layer Architecture

### SensorDataService Class

**Key Methods**:

1. **`handleDelayedData()`**: Process late-arriving sensor data
2. **`calculateSmartHistoricalAverage()`**: Generate intelligent fallback data
3. **`getDataQualityStats()`**: Provide quality metrics and monitoring
4. **`replaceFallbackData()`**: Replace fallback with real data
5. **`insertBackfilledData()`**: Handle very late data arrivals

**Weighted Average Calculation**:
```php
private function calculateWeightedAverages(Collection $historicalData, Carbon $targetTime): array
{
    $totalWeight = 0;
    $weightedSums = ['kekeruhan' => 0, 'keasaman' => 0, 'suhu' => 0];

    foreach ($historicalData as $record) {
        $timeWeight = max(0.1, 1 - ($timeDiffMinutes / 60));
        $dayWeight = $recordTime->dayOfWeek === $targetTime->dayOfWeek ? 1.2 : 1.0;
        $recencyWeight = max(0.5, 1 - ($daysDiff / 30));

        $combinedWeight = $timeWeight * $dayWeight * $recencyWeight;
        // Apply weights to calculate averages...
    }
}
```

## Benefits and Features

### 1. **Data Continuity**
- **Zero Data Gaps**: Dashboard always displays data for every expected time slot
- **Seamless Experience**: Users unaware of sensor failures during demos
- **Real-time Updates**: Fallback data generated within 2 minutes of missing data

### 2. **Intelligent Predictions**
- **Historical Patterns**: Leverages 7-day historical data for realistic predictions
- **Time-aware**: Considers daily patterns and seasonal variations
- **Adaptive Noise**: Realistic variance based on historical data behavior

### 3. **Robust Data Handling**
- **Late Data Recovery**: Automatically replaces fallback data when real data arrives
- **Flexible Thresholds**: Configurable timing parameters for different use cases
- **Data Integrity**: Clear labeling of data sources for transparency

### 4. **Quality Monitoring**
- **Quality Metrics**: Real-time tracking of data source percentages
- **Quality Score**: Overall data reliability indicator
- **Historical Analysis**: Quality trends over time periods

### 5. **Professional Standards**
- **Industry Best Practices**: Standard approach for IoT data reliability
- **Configurable Parameters**: Easy adjustment for different environments
- **Comprehensive Logging**: Full audit trail of fallback operations

## Implementation Phases

### Phase 1: Basic Fallback (Implemented)
- Database schema updates
- Configuration system
- Laravel job scheduling
- Simple historical average calculation
- Basic fallback mechanism

### Phase 2: Smart Fallback (Implemented)
- Enhanced SensorDataService
- Weighted historical calculations
- Adaptive noise injection
- Delayed data handling
- Data quality monitoring
- API enhancements

### Phase 3: Dashboard Integration (Not in Scope)
- Frontend indicators for estimated data
- Data quality visualization
- Real vs estimated data toggles
- Quality metrics dashboard

## Testing Scenarios

### 1. **Normal Operation**
- Sensor sends data every minute
- All data marked as `REAL_TIME`
- No fallback generation

### 2. **Missing Data Scenario**
- Sensor offline for 3+ minutes
- Job detects missing slots after 2-minute delay
- Generates `FALLBACK` data using historical patterns

### 3. **Delayed Data Scenario**
- Sensor sends data 3 minutes late
- System detects delay via `reading_timestamp`
- Replaces existing fallback data with real data

### 4. **Very Late Data Scenario**
- Sensor sends data 10 minutes late
- System inserts as `BACKFILLED` data
- Maintains both fallback and real data records

### 5. **Bootstrap Scenario**
- First 3 days of operation (insufficient historical data)
- Uses configured default value ranges
- Transitions to historical averages after 3+ days

## Configuration for Different Environments

### Demo Environment (Current)
```php
'trigger_delay_minutes' => 2,        // Quick response for demos
'minimum_historical_days' => 3,      // Fast bootstrap
'enable_logging' => true,            // Full visibility
```

### Production Environment (Future)
```php
'trigger_delay_minutes' => 5,        // More tolerance for network issues
'minimum_historical_days' => 7,      // More conservative bootstrap
'enable_logging' => false,           // Performance optimization
```

## Monitoring and Maintenance

### Log Messages
- **Missing Data Detection**: Count and timestamps of missing slots
- **Fallback Generation**: Historical vs default value usage
- **Delayed Data Processing**: Delay duration and handling strategy

### Performance Considerations
- **Database Indexing**: Ensure indexes on `created_at`, `data_source` columns
- **Historical Query Optimization**: Limited to relevant time windows
- **Job Queue Management**: Prevent overlapping executions with `withoutOverlapping()`

### Maintenance Tasks
- **Regular Quality Review**: Monitor data quality trends
- **Configuration Tuning**: Adjust parameters based on sensor behavior
- **Historical Data Cleanup**: Archive old data to maintain performance

## Conclusion

The implemented sensor fallback mechanism provides a robust, intelligent solution for maintaining data continuity in IoT monitoring systems. It balances simplicity with sophistication, offering immediate value for demo scenarios while providing a foundation for production deployment with enhanced reliability and monitoring capabilities.

The two-phase implementation ensures progressive enhancement from basic fallback to smart predictive capabilities, making the system both immediately functional and professionally scalable.