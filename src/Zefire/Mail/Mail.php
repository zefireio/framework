<?php

namespace Zefire\Mail;

class Mail
{
	/**
     * Stores the mail driver instance.
     *
     * @var object
     */
    protected $driver;
	/**
     * Stores a list of senders.
     *
     * @var array
     */
    protected $from = [];
    /**
     * Stores a list of receipients.
     *
     * @var array
     */
    protected $to = [];
    /**
     * Stores a list of carbon copy receipients.
     *
     * @var array
     */
    protected $cc = [];
    /**
     * Stores a list of blind carbon copy receipients.
     *
     * @var array
     */
    protected $bcc = [];
    /**
     * Stores a list of "reply to" receipients.
     *
     * @var array
     */
    protected $replyTo = [];
    /**
     * Stores the subject.
     *
     * @var string
     */
    protected $subject = null;
    /**
     * Stores the message.
     *
     * @var string
     */
    protected $message = null;
    /**
     * Stores a flag to send email as HTML or plain text.
     *
     * @var boolean
     */
    protected $html = true;    
    /**
     * Creates a new mail instance.
     *
     * @return void
     */
	public function __construct()
    {
        try {
            $this->driver = \App::make(\App::config('mail.mailgun.driver'));
        } catch (\Exception $e) {
            $this->driver = \App::make('Zefire\Mail\MailGunAdapter');
        }        
    }
    /**
     * Adds a sender.
     *
     * @param  string $address
     * @param  string $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        array_push($this->from, ['address' => $address, 'name' => $name]);
        return $this;
    }
    /**
     * Adds a receipient.
     *
     * @param  string $address
     * @param  string $name
     * @return $this
     */
    public function to($address, $name = null)
    {
        array_push($this->to, ['address' => $address, 'name' => $name]);
        return $this;
    }
    /**
     * Adds a carbon copy receipient.
     *
     * @param  string $address
     * @param  string $name
     * @return $this
     */
    public function cc($address, $name = null)
    {
        array_push($this->cc, ['address' => $address, 'name' => $name]);
        return $this;
    }
    /**
     * Adds a blind carbon copy receipient.
     *
     * @param  string $address
     * @param  string $name
     * @return $this
     */
    public function bcc($address, $name = null)
    {
        array_push($this->bcc, ['address' => $address, 'name' => $name]);
        return $this;
    }
    /**
     * Adds a "reply to" receipient.
     *
     * @param  string $address
     * @param  string $name
     * @return $this
     */
    public function replyTo($address, $name = null)
    {
        array_push($this->replyTo, ['address' => $address, 'name' => $name]);
        return $this;
    }
    /**
     * Adds a subject.
     *
     * @param  string $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
    /**
     * Adds a message.
     *
     * @param  string $message
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }
    /**
     * Flags the mail to sent as HTML.
     *
     * @return $this
     */
    public function html()
    {
        $this->html = true;
        return $this;
    }
    /**
     * Flags the mail to sent as plain text.
     *
     * @return $this
     */
    public function plainText()
    {
        $this->html = false;
        return $this;
    }
    /**
     * Sends the email.
     *
     * @return $mixed
     */
    public function send()
    {
        return $this->driver->send(
            $this->from,
            $this->to,
            $this->cc,
            $this->bcc,
            $this->replyTo,
            $this->subject,
            $this->message,
            $this->html
        );
    }    
}