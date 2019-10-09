<?php

namespace iBrand\Coterie\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


/**
 * 发表问题消息通知
 * Class PublishQuestion
 * @package iBrand\Coterie\Core\Notifications
 */
class PublishQuestion extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($coterie,$user,$question,$answer_user)
    {

        $this->type='PublishQuestion';
        $this->coterie=$coterie;
        $this->user=$user;
        $this->question=$question;
        $this->answer_user=$answer_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type'=>$this->type,
            'coterie'=>$this->coterie,
            'user'=>$this->user,
            'question'=>$this->question,
            'answer_user'=>$this->answer_user
        ];
    }
}
