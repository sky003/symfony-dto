<?php

namespace App\Dto\Request;


class AbstractPropertyChangeTracker implements PropertyChangeTrackerInterface
{
    /**
     * @var bool
     */
    protected $propertyChangeTrackerEnabled = true;
    /**
     * @var array
     */
    protected $propertyChangeSet = [];

    /**
     * {@inheritdoc}
     */
    public function isPropertyChanged(string $propertyName): bool
    {
        return isset($this->propertyChangeSet[$propertyName]);
    }

    /**
     * {@inheritdoc}
     */
    public function isTrackerEnabled(): bool
    {
        return $this->propertyChangeTrackerEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackerEnabled(bool $enabled): void
    {
        $this->propertyChangeTrackerEnabled = $enabled;
    }

    protected function registerPropertyChanged(string $propertyName): void
    {
        if ($this->isTrackerEnabled()) {
            $this->propertyChangeSet[$propertyName] = true;
        }
    }
}
