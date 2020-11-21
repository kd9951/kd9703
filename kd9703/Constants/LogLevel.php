<?php
namespace Kd9703\Constants;

class LogLevel extends Enum
{
    const DEBUG        = 'debug';
    const KPI          = 'kpi';
    const INFO         = 'info';
    const NOTICE       = 'notice';
    const MEDIA_ACCESS = 'media_access';
    const JOB          = 'job';
    const WARNING      = 'warning';
    const ERROR        = 'error';
    const CRITICAL     = 'critical';
    const ALERT        = 'alert';
    const EMERGENCY    = 'emergency';

    const LIST = [
        self::DEBUG,
        self::KPI,
        self::INFO,
        self::NOTICE,
        self::MEDIA_ACCESS,
        self::JOB,
        self::WARNING,
        self::ERROR,
        self::CRITICAL,
        self::ALERT,
        self::EMERGENCY,
    ];
}
