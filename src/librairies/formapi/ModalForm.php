<?php

declare(strict_types = 1);

namespace stats\librairies\formapi;

use pocketmine\form\FormValidationException;

class ModalForm extends Form {

    /**
     * @var string
     */
    private string $content = "";

    /**
     * @param callable|null $callable
     * @noinspection DuplicatedCode
     */
    public function __construct(?callable $callable) {
        parent::__construct($callable);
        $this->data["type"] = "modal";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
        $this->data["button1"] = "";
        $this->data["button2"] = "";
    }

    /**
     * @param $data
     * @return void
     * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
     */
    public function processData(&$data): void {
        if (!is_bool($data) && !is_null($data)) {
            throw new FormValidationException("Expected a boolean response, got " . gettype($data));
        }
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void {
        $this->data["title"] = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->data["title"];
    }

    /**
     * @return string
     * @return string
     */
    public function getContent(): string {
        return $this->data["content"];
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void {
        $this->data["content"] = $content;
    }

    /**
     * @param string $text
     * @return void
     */
    public function setButton1(string $text): void {
        $this->data["button1"] = $text;
    }

    /**
     * @return string
     * @return string
     */
    public function getButton1(): string {
        return $this->data["button1"];
    }

    /**
     * @param string $text
     * @return void
     */
    public function setButton2(string $text): void {
        $this->data["button2"] = $text;
    }

    /**
     * @return string
     * @return string
     */
    public function getButton2(): string {
        return $this->data["button2"];
    }

}
