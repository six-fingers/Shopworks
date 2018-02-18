<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
   protected $table = 'rota_slot_staff';

   /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get Staff Id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->attributes['staffid'];
    }

    /**
     * Get Staff Id
     *
     * @return int
     */
    public function getDayNumber()
    {
        return $this->attributes['daynumber'];
    }

    /**
     * Get Staff Id
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->attributes['starttime'];
    }

    /**
     * Get Staff Id
     *
     * @return string
     */
    public function getEndTime()
    {
        return $this->attributes['endtime'];
    }
}
