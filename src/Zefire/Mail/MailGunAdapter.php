<?php

namespace Zefire\Mail;

class MailGunAdapter
{
	/**
     * Stores the driver's configuration.
     *
     * @var array
     */
    protected $config;
    /**
     * Stores the mail's data.
     *
     * @var array
     */
    protected $data = [];
    /**
     * Creates a new Mailgun adapter instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = \App::config('mail.mailgun');
    }
    /**
     * Sends an email through Mailgun's API.
     *
     * @param  array  $from
     * @param  array  $to
     * @param  array  $cc
     * @param  array  $bcc
     * @param  array  $replyTo
     * @param  string $subject
     * @param  string $message
     * @param  bool   $html
     * @return array
     */
    public function send($from = [], $to = [], $cc = [], $bcc = [], $replyTo = [], $subject, $message, $html)
    {
        
        if (!empty($from)) {
            $this->data['from'] = $this->formatIdentity($from);
        } else {
            throw new \Exception('Email cannot be sent without a sender.');
        }
        if (!empty($to)) {
            $this->data['to'] = $this->formatIdentity($to);
        } else {
            throw new \Exception('Email cannot be sent without a receipient.');
        }
        if (!empty($cc)) {
            $this->data['cc'] = $this->formatIdentity($cc);
        }
        if (!empty($bcc)) {
            $this->data['bcc'] = $this->formatIdentity($bcc);
        }
        if (!empty($replyTo)) {
            $this->data['h:Reply-To'] = $this->formatIdentity($replyTo);
        }
        if ($subject != null && $subject != '') {
            $this->data['subject'] = $subject; 
        } else {
            throw new \Exception('Email cannot be sent without a subject.');
        }
        if ($message != null && $message != '') {
            if ($html === true) {
            $this->data['html'] = $message;
            } else {
                $this->data['text'] = $message;
            }    
        } else {
            throw new \Exception('Email cannot be sent without a message.');
        }        
        if (isset($this->config['tracking']) && $this->config['tracking'] === true) {
            $this->data['o:tracking'] = 'yes';
        }
        if (isset($this->config['clicks']) && $this->config['clicks'] === true) {
            $this->data['o:tracking-clicks'] = 'yes';
        }
        if (isset($this->config['opens']) && $this->config['opens'] === true) {
            $this->data['o:tracking-opens'] = 'yes';
        }
        $response = $this->call();
        return (is_array($response) && $response['message'] == 'Queued. Thank you.') ? true : false;
    }
    /**
     * Converts a name email pair to a string.
     *
     * @param  array  $array
     * @return string
     */
    protected function formatIdentity($array)
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (isset($array[$key]['name']) && $array[$key]['name'] != '' && $array[$key]['name'] != null) {
                $string .= $array[$key]['name'] . '<' . $array[$key]['address'] . '>,';
            } else {
                $string .= $array[$key]['address'] . ',';
            }
        }
        return substr($string, 0, -1);
    }
    /**
     * Send the actual email to mailgun with a Curl call.
     *
     * @return array
     */
    protected function call()
    {
        $session = curl_init($this->config['endpoint'] . '/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:' . $this->config['key']);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);
        return $results;
    }    
}