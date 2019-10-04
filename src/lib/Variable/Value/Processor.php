<?php

namespace ContextualCode\EzPlatformContentVariables\Variable\Value;

class Processor
{
    /** @var array */
    protected $callbacks = [];

    public function __construct(iterable $callbacks)
    {
        foreach ($callbacks as $callback) {
            $this->registerCallback($callback);
        }
    }

    protected function registerCallback(Callback $callback): void
    {
        $identifier = $callback->getIdentifier();
        $this->callbacks[$identifier] = $callback;
    }

    public function getCallback(string $identifier): ?Callback
    {
        return isset($this->callbacks[$identifier]) ? $this->callbacks[$identifier] : null;
    }

    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    public function getCallbackChoices(): array
    {
        $choices = [];
        foreach ($this->getCallbacks() as $callback) {
            $choices[$callback->getName()] = $callback->getIdentifier();
        }

        return $choices;
    }
}