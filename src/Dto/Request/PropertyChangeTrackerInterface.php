<?php

declare(strict_types = 1);

namespace App\Dto\Request;

/**
 * Interface PropertyChangeTrackerInterface.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface PropertyChangeTrackerInterface
{
    /**
     * Whether the property was changed.
     *
     * @param string $propertyName
     *
     * @return bool
     */
    public function isPropertyChanged(string $propertyName): bool;

    /**
     * Whether track changes or not.
     *
     * @return bool
     */
    public function isTrackerEnabled(): bool;

    /**
     * Enable/disable tracker.
     *
     * @param bool $enabled
     */
    public function setTrackerEnabled(bool $enabled): void;
}
