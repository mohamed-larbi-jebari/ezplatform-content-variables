<?php

namespace ContextualCode\EzPlatformContentVariables\Variable\Value;

class Processor
{
    /** @var \ContextualCode\EzPlatformContentVariables\Variable\Value\Callback[] */
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

    /**
     * @return \ContextualCode\EzPlatformContentVariables\Variable\Value\Callback|null
     */
    public function getCallback(string $identifier): ?Callback
    {
        return $this->callbacks[$identifier] ?? null;
    }

    /**
     * @return \ContextualCode\EzPlatformContentVariables\Variable\Value\Callback[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * @return string[]
     */
    public function getCallbackChoices(): array
    {
        $choices = [];
        foreach ($this->getCallbacks() as $callback) {
            $choices[$callback->getName()] = $callback->getIdentifier();
        }

        return $choices;
    }
}
