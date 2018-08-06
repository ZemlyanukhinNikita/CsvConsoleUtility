<?php

class Options
{
    private $optionInput;
    private $optionConfig;
    private $optionOutput;
    private $optionDelimiter;
    private $optionSkipFirst;
    private $optionStrict;
    private $optionHelp;

    /**
     * @return mixed
     */
    public function getOptionInput()
    {
        return $this->optionInput;
    }

    /**
     * @param mixed $optionInput
     */
    public function setOptionInput($optionInput)
    {
        $this->optionInput = $optionInput;
    }

    /**
     * @return mixed
     */
    public function getOptionConfig()
    {
        return $this->optionConfig;
    }

    /**
     * @param mixed $optionConfig
     */
    public function setOptionConfig($optionConfig)
    {
        $this->optionConfig = $optionConfig;
    }

    /**
     * @return mixed
     */
    public function getOptionOutput()
    {
        return $this->optionOutput;
    }

    /**
     * @param mixed $optionOutput
     */
    public function setOptionOutput($optionOutput)
    {
        $this->optionOutput = $optionOutput;
    }

    /**
     * @return mixed
     */
    public function getOptionDelimiter()
    {
        return $this->optionDelimiter;
    }

    /**
     * @param mixed $optionDelimiter
     */
    public function setOptionDelimiter($optionDelimiter)
    {
        $this->optionDelimiter = $optionDelimiter;
    }

    /**
     * @return mixed
     */
    public function getOptionSkipFirst()
    {
        return $this->optionSkipFirst;
    }

    /**
     * @param mixed $optionSkipFirst
     */
    public function setOptionSkipFirst($optionSkipFirst)
    {
        $this->optionSkipFirst = $optionSkipFirst;
    }

    /**
     * @return mixed
     */
    public function getOptionStrict()
    {
        return $this->optionStrict;
    }

    /**
     * @param mixed $optionStrict
     */
    public function setOptionStrict($optionStrict)
    {
        $this->optionStrict = $optionStrict;
    }

    /**
     * @return mixed
     */
    public function getOptionHelp()
    {
        return $this->optionHelp;
    }

    /**
     * @param mixed $optionHelp
     */
    public function setOptionHelp($optionHelp)
    {
        $this->optionHelp = $optionHelp;
    }

}