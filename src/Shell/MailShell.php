<?php
namespace App\Shell;

use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\Component\MailComponent;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;

/**
 * Mail shell command.
 */
class MailShell extends Shell
{
    protected $mail;
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        //$this->Mail = new MailComponent(new ComponentRegistry(), []);
        $this->mail = new Email();
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        //$this->out($this->OptionParser->help());

        while (true){
            $job = null;
            $jobs = Cache::read('jobs');
            if ($jobs == false){
                sleep(10);
            //    $this->Mail->send("masum0009@gmail.com","test email",['test'=>'test value'],'test');
            //    $this->out("sent test email");
                continue;
            }

            if ($jobs !== false)
                $job = array_pop($jobs);

            Cache::write('jobs', $jobs);
            if ($job){
                // $db =  ConnectionManager::get('default')->config();
                // ConnectionManager::drop('default');
                // $db['database'] = "clcknshop_{$job['store_name']}";
                // $dsn = "mysql://{$db['username']}:{$db['password']}@localhost/{$db['database']}";
                // ConnectionManager::config('default', ['url' => $dsn]);

                if ($job['task'] == "mail"){
                    $data = $job['data'];
                    //$this->Mail->initConfig();
                    //$mail = $this->Mail->send($data['email'], $data['subject'], $data, $data['template']);
                    //if (!empty($mail)) $this->out($mail);

                    $smtp  = $data['smtp'];
                    $to = $data['to'];
                    $subject = $data['subject'];
                    $message = $data['message'];
                    $sender = $data['sender'];
                    TransportFactory::drop('smtp');
                    TransportFactory::setConfig('smtp', $smtp);
                    try {
                        $email = $this->mail->reset()
                            ->transport('smtp')
                            ->emailFormat('html')
                            ->from($sender)
                            ->to($to)
                            ->subject($subject);
                            if(isset($data['attachments'])){
                              foreach($data['attachments'] as $attachement){
                                $email->attachments([
                                basename($attachement) => [
                                    'file' => $attachement]
                                ]);
                              }
                            }
                            $email->send($message);
                           $this->out("Successfully sent email ");
                    }catch (\Exception $exception){
                        $this->out($exception->getMessage());
                    }
                    if(isset($data['attachments'])){
                        foreach($data['attachments'] as $attachement){
                         @unlink($attachement);
                        }
                    } 
                }
            }

          //  $this->out("Success ");
        }



    }
}
