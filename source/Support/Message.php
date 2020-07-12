<?php

namespace Source\Support;

/**
 * Class Message
 *
 * @author André de Brito <andredebrito1990@gmail.com>
 * @package Source\Support
 */
class Message {

    /** @var string */
    private $text;

    /** @var string */
    private $type;
    
    /** @var string */
    private $icon;

    /** @var string */
    private $before;

    /** @var string */
    private $after;

    /**
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

    /** Returns the message text
     * @return string
     */
    public function getText(): ?string {
        return $this->before . $this->text . $this->after;
    }

    /** Returns the mesage type
     * @return string
     */
    public function getType(): ?string {
        return $this->type;
    }
    
    /**
     * 
     * @param string $text
     * @return Message
     */
    public function before(string $text) : Message {
        $this->before = $text;
        return $this;        
    }
    
    /**
     * 
     * @param string $text
     * @return Message
     */
     public function after(string $text) : Message {
        $this->after = $text;
        return $this;        
    }

    /** Creates a info message
     * @param string $message
     * @return Message
     */
    public function info(string $message): Message {
        $this->type = CONF_MESSAGE_INFO;
        $this->icon = CONF_MESSAGE_ICONS["icon-info"];
        $this->text = $this->filter($message);
        return $this;
    }

    /** Creates a success message
     * @param string $message
     * @return Message
     */
    public function success(string $message): Message {
        $this->type = CONF_MESSAGE_SUCCESS;
        $this->icon = CONF_MESSAGE_ICONS["icon-success"];
        $this->text = $this->filter($message);
        return $this;
    }

    /** Creates a warning message
     * @param string $message
     * @return Message
     */
    public function warning(string $message): Message {
        $this->type = CONF_MESSAGE_WARNING;
        $this->icon = CONF_MESSAGE_ICONS["icon-warning"];
        $this->text = $this->filter($message);
        return $this;
    }

    /** Creates a error message
     * @param string $message
     * @return Message
     */
    public function error(string $message): Message {
        $this->type = CONF_MESSAGE_ERROR;
        $this->icon = CONF_MESSAGE_ICONS["icon-error"];
        $this->text = $this->filter($message);
        return $this;
    }

    /** Render the message
     * @return string
     */
    public function render(): string {
        return "<div class='" . CONF_MESSAGE_CLASS . " {$this->getType()} text-center'>{$this->icon} {$this->getText()}</div>";
    }

    /** Return a message on a json
     * @return string
     */
    public function json(): string {
        return json_encode(["error" => $this->getText()]);
    }

    /** Filter and sanitize the message
     * @param string $message
     * @return string
     */
    private function filter(string $message): string {
        return filter_var($message, FILTER_SANITIZE_STRIPPED);
    }

}
