<?php

namespace matthiasott\internetarchive\models;

use Craft;
use craft\base\Model;

/**
 * Internet Archive settings
 */
class Settings extends Model
{
    public bool $sendUrls = true;
}
