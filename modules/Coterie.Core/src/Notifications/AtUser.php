<?php

namespace iBrand\Coterie\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


/**
 * 动态@某人消息通知
 * Class AtUser
 * @package iBrand\Coterie\Core\Notifications
 */
class AtUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($coterie,$user,$content,$at_user)
    {
        //
        $this->type='AtUser';
        $this->coterie=$coterie;
        $this->user=$user;
        $this->content=$content;
        $this->at_user=$at_user;
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
            'content'=>$this->content,
            'at_user'=>$this->at_user
         ];
    }
}
