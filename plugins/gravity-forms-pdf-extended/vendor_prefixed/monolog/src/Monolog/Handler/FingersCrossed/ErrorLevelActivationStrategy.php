<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GFPDF_Vendor\Monolog\Handler\FingersCrossed;

use GFPDF_Vendor\Monolog\Logger;
/**
 * Error level based activation strategy.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ErrorLevelActivationStrategy implements \GFPDF_Vendor\Monolog\Handler\FingersCrossed\ActivationStrategyInterface
{
    /**
     * @var int
     */
    private $actionLevel;
    /**
     * @param int|string $actionLevel Level or name or value
     */
    public function __construct($actionLevel)
    {
        $this->actionLevel = \GFPDF_Vendor\Monolog\Logger::toMonologLevel($actionLevel);
    }
    public function isHandlerActivated(array $record) : bool
    {
        return $record['level'] >= $this->actionLevel;
    }
}
