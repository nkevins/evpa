<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepFieldSource;
use App\Models\Enums\PirepState;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

/**
 * Class Pirep
 *
 * @property string      id
 * @property string      flight_number
 * @property string      route_code
 * @property string      route_leg
 * @property int         airline_id
 * @property int         user_id
 * @property int         aircraft_id
 * @property Aircraft    aircraft
 * @property Airline     airline
 * @property Airport     arr_airport
 * @property string      arr_airport_id
 * @property Airport     dpt_airport
 * @property string      dpt_airport_id
 * @property Carbon      block_off_time
 * @property Carbon      block_on_time
 * @property int         block_time
 * @property int         flight_time    In minutes
 * @property int         planned_flight_time
 * @property float       distance
 * @property float       planned_distance
 * @property string      route
 * @property int         score
 * @property User        user
 * @property Flight|null flight
 * @property Collection  fields
 * @property int         status
 * @property bool        state
 * @property Carbon      submitted_at
 * @property Carbon      created_at
 * @property Carbon      updated_at
 * @property bool        read_only
 * @property Acars       position
 * @property Acars[]     acars
 * @property mixed       cancelled
 */
class Pirep extends Model
{
    use HashIdTrait;

    public $table = 'pireps';
    public $incrementing = false;

    /** The form wants this */
    public $hours;
    public $minutes;

    protected $fillable = [
        'id',
        'user_id',
        'airline_id',
        'aircraft_id',
        'flight_number',
        'route_code',
        'route_leg',
        'dpt_airport_id',
        'arr_airport_id',
        'alt_airport_id',
        'level',
        'distance',
        'planned_distance',
        'block_time',
        'flight_time',
        'planned_flight_time',
        'zfw',
        'block_fuel',
        'fuel_used',
        'landing_rate',
        'route',
        'notes',
        'score',
        'source',
        'source_name',
        'flight_type',
        'state',
        'status',
        'block_off_time',
        'block_on_time',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id'             => 'integer',
        'airline_id'          => 'integer',
        'aircraft_id'         => 'integer',
        'level'               => 'integer',
        'distance'            => 'float',
        'planned_distance'    => 'float',
        'block_time'          => 'integer',
        'flight_time'         => 'integer',
        'planned_flight_time' => 'integer',
        'zfw'                 => 'float',
        'block_fuel'          => 'float',
        'fuel_used'           => 'float',
        'landing_rate'        => 'float',
        'score'               => 'integer',
        'source'              => 'integer',
        'state'               => 'integer',
    ];

    public static $rules = [
        'airline_id'     => 'required|exists:airlines,id',
        'aircraft_id'    => 'required|exists:aircraft,id',
        'flight_number'  => 'required',
        'dpt_airport_id' => 'required',
        'arr_airport_id' => 'required',
        'notes'          => 'nullable',
        'route'          => 'nullable',
    ];

    /**
     * If a PIREP is in these states, then it can't be changed.
     */
    public static $read_only_states = [
        //PirepState::PENDING,
        PirepState::ACCEPTED,
        PirepState::REJECTED,
        PirepState::CANCELLED,
    ];

    /**
     * Get the flight ident, e.,g JBU1900
     *
     * @return string
     */
    public function getIdentAttribute(): string
    {
        //$flight_id = $this->airline->code;
        $flight_id = $this->flight_number;

        if (filled($this->route_code)) {
            $flight_id .= '/C'.$this->route_code;
        }

        if (filled($this->route_leg)) {
            $flight_id .= '/L'.$this->route_leg;
        }

        return $flight_id;
    }

    /**
     * Return the block off time in carbon format
     *
     * @return Carbon|null
     */
    public function getBlockOffTimeAttribute()
    {
        if (array_key_exists('block_off_time', $this->attributes)) {
            return new Carbon($this->attributes['block_off_time']);
        }
    }

    /**
     * Return the block on time
     *
     * @return Carbon|null
     */
    public function getBlockOnTimeAttribute()
    {
        if (array_key_exists('block_on_time', $this->attributes)) {
            return new Carbon($this->attributes['block_on_time']);
        }
    }

    /**
     * Return the block on time
     *
     * @return Carbon
     */
    public function getSubmittedAtAttribute()
    {
        if (array_key_exists('submitted_at', $this->attributes)) {
            return new Carbon($this->attributes['submitted_at']);
        }
    }

    /**
     * Return a new Length unit so conversions can be made
     *
     * @return int|Distance
     */
    public function getDistanceAttribute()
    {
        if (!array_key_exists('distance', $this->attributes)) {
            return 0;
        }

        try {
            $distance = (float) $this->attributes['distance'];
            if ($this->skip_mutator) {
                return $distance;
            }

            return new Distance($distance, config('phpvms.internal_units.distance'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }

    /**
     * Set the distance unit, convert to our internal default unit
     *
     * @param $value
     */
    public function setDistanceAttribute($value): void
    {
        if ($value instanceof Distance) {
            $this->attributes['distance'] = $value->toUnit(
                config('phpvms.internal_units.distance')
            );
        } else {
            $this->attributes['distance'] = $value;
        }
    }

    /**
     * Return if this PIREP can be edited or not
     */
    public function getReadOnlyAttribute(): bool
    {
        return \in_array($this->state, static::$read_only_states, true);
    }

    /**
     * Return a new Fuel unit so conversions can be made
     *
     * @return int|Fuel
     */
    public function getFuelUsedAttribute()
    {
        if (!array_key_exists('fuel_used', $this->attributes)) {
            return 0;
        }

        try {
            $fuel_used = (float) $this->attributes['fuel_used'];

            return new Fuel($fuel_used, config('phpvms.internal_units.fuel'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }

    /**
     * Return the planned_distance in a converter class
     *
     * @return int|Distance
     */
    public function getPlannedDistanceAttribute()
    {
        if (!array_key_exists('planned_distance', $this->attributes)) {
            return 0;
        }

        try {
            $distance = (float) $this->attributes['planned_distance'];
            if ($this->skip_mutator) {
                return $distance;
            }

            return new Distance($distance, config('phpvms.internal_units.distance'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }

    /**
     * Return the flight progress in a percent.
     */
    public function getProgressPercentAttribute()
    {
        $upper_bound = $this->distance['nmi'];
        if ($this->planned_distance) {
            $upper_bound = $this->planned_distance['nmi'];
        }

        if (!$upper_bound) {
            $upper_bound = 1;
        }

        return round(($this->distance['nmi'] / $upper_bound) * 100, 0);
    }

    /**
     * Get the pirep_fields and then the pirep_field_values and
     * merge them together. If a field value doesn't exist then add in a fake one
     */
    public function getFieldsAttribute()
    {
        $custom_fields = PirepField::all();
        $field_values = PirepFieldValue::where('pirep_id', $this->id)->get();

        // Merge the field values into $fields
        foreach ($custom_fields as $field) {
            $has_value = $field_values->firstWhere('slug', $field->slug);
            if (!$has_value) {
                $field_values->push(new PirepFieldValue([
                    'pirep_id' => $this->id,
                    'name'     => $field->name,
                    'slug'     => $field->slug,
                    'value'    => '',
                    'source'   => PirepFieldSource::MANUAL,
                ]));
            }
        }

        return $field_values->sortBy('source');
    }

    /**
     * Look up the flight, based on the PIREP flight info
     *
     * @return Flight|null
     */
    public function getFlightAttribute(): ?Flight
    {
        $where = [
            'airline_id'    => $this->airline_id,
            'flight_number' => $this->flight_number,
            'active'        => true,
        ];

        if (filled($this->route_code)) {
            $where['route_code'] = $this->route_code;
        }

        if (filled($this->route_leg)) {
            $where['route_leg'] = $this->route_leg;
        }

        return Flight::where($where)->first();
    }

    /**
     * Set the amount of fuel used
     *
     * @param $value
     */
    public function setFuelUsedAttribute($value): void
    {
        if ($value instanceof Fuel) {
            $this->attributes['fuel_used'] = $value->toUnit(
                config('phpvms.internal_units.fuel')
            );
        } else {
            $this->attributes['fuel_used'] = $value;
        }
    }

    /**
     * Set the distance unit, convert to our internal default unit
     *
     * @param $value
     */
    public function setPlannedDistanceAttribute($value): void
    {
        if ($value instanceof Distance) {
            $this->attributes['planned_distance'] = $value->toUnit(
                config('phpvms.internal_units.distance')
            );
        } else {
            $this->attributes['planned_distance'] = $value;
        }
    }

    /**
     * Do some cleanup on the route
     *
     * @param $route
     */
    public function setRouteAttribute($route): void
    {
        $route = strtoupper(trim($route));
        $this->attributes['route'] = $route;
    }

    /**
     * Return if this is cancelled or not
     */
    public function getCancelledAttribute(): bool
    {
        return $this->state === PirepState::CANCELLED;
    }

    /**
     * Check if this PIREP is allowed to be updated
     *
     * @return bool
     */
    public function allowedUpdates(): bool
    {
        return !$this->getReadOnlyAttribute();
    }

    /**
     * Return a custom field value
     *
     * @param $field_name
     *
     * @return string
     */
    public function field($field_name): string
    {
        $field = $this->fields->where('name', $field_name)->first();
        if ($field) {
            return $field['value'];
        }

        return '';
    }

    /**
     * Foreign Keys
     */
    public function acars()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
            ->where('type', AcarsType::FLIGHT_PATH)
            ->orderBy('created_at', 'asc')
            ->orderBy('sim_time', 'asc');
    }

    public function acars_logs()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
            ->where('type', AcarsType::LOG)
            ->orderBy('created_at', 'desc')
            ->orderBy('sim_time', 'asc');
    }

    public function acars_route()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
            ->where('type', AcarsType::ROUTE)
            ->orderBy('order', 'asc');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo(Airport::class, 'arr_airport_id');
    }

    public function alt_airport()
    {
        return $this->belongsTo(Airport::class, 'alt_airport_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo(Airport::class, 'dpt_airport_id');
    }

    public function comments()
    {
        return $this->hasMany(PirepComment::class, 'pirep_id')
            ->orderBy('created_at', 'desc');
    }

    public function fares()
    {
        return $this->hasMany(PirepFare::class, 'pirep_id');
    }

    public function field_values()
    {
        return $this->hasMany(PirepFieldValue::class, 'pirep_id');
    }

    public function pilot()
    {
        return $this->user();
    }

    /**
     * Relationship that holds the current position, but limits the ACARS
     *  relationship to only one row (the latest), to prevent an N+! problem
     */
    public function position()
    {
        return $this->hasOne(Acars::class, 'pirep_id')
            ->where('type', AcarsType::FLIGHT_PATH)
            ->latest();
    }

    public function transactions()
    {
        return $this->hasMany(JournalTransaction::class, 'ref_model_id')
            ->where('ref_model', __CLASS__)
            ->orderBy('credit', 'desc')
            ->orderBy('debit', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
