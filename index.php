<?php 

class GetMail
{
    private $host = '';
    private $user = '';
    private $pass = '';
    private $type = '';
    private $connect = '';
    private $mail_obj = '';
    private $num = '';

    
    function __construct($host,$username = 'shangyinwl@163.com',$password = 'TFNSWHMMOVMITCHD',$type)
    {
        if($type != 'imap' && $type != 'pop3') {
            echo 'Error: Undefined protocol type <br>'; exit;
        }

        if($type == 'imap') {
            $this->connect = '{'. $host .':143}INBOX';
        } else {
            $this->connect = '{'. $host .':110/pop3}INBOX';
        }

        $obj = imap_open($this->connect, $username, $password);
        if(!$obj) {
            echo 'Error: Login failed ' . imap_last_error(); exit;
        } else {
            $this->mail_obj = $obj;
        }
    }

    /**
     * 获取邮件总数
     * @return int 条数
     */
    public function GmailNum()
    {
        $this->num = imap_num_msg($this->mail_obj);
        return $this->num;
    }

    /**
     * 获取邮件正文
     * @param int 邮件id HTML格式
     * @return string body
     */
    public function GMailBody($id,$is_html = false)
    {
        $this->GBumBool($id);
        $charset = $this->Gcharset($id);
        $type = imap_fetchstructure($this->mail_obj,$id);
        if($type->subtype == 'HTML')
        {
            return imap_qprint(imap_fetchbody($this->mail_obj, $id, 1));
        }else {
            if(isset($type->parts) && $type->parts[0]->subtype == 'HTML')
            {
                return imap_qprint(imap_fetchbody($this->mail_obj, $id, 1));
            }
        }

        if($type->subtype == 'ALTERNATIVE')
        {
            if(!$is_html) {
                return mb_convert_encoding(base64_decode(imap_fetchbody($this->mail_obj, $id, 1)),'UTF-8', $charset);
            }else {
                return mb_convert_encoding(base64_decode(imap_fetchbody($this->mail_obj, $id, 2)),'UTF-8', $charset);
            }
        }       
    }

    /**
     * 获取邮件头信息
     * @param int 邮件id
     * @return array from发送者 formName发送者名称 Subject标题 time 
     */
    public function GMailTit($id)
    {
        $this->GBumBool($id);

        $str = imap_headerinfo($this->mail_obj,$id);

        $arr = [
            'from' => $str->from[0]->mailbox . '@' .$str->from[0]->host,
            'fromName' => mb_convert_encoding(imap_mime_header_decode($str->from[0]->personal)[0]->text, imap_mime_header_decode($str->from[0]->personal)[0]->charset, 'UTF-8'),
            'Subject' => '',
            'time' => $str->udate

        ];
        foreach (imap_mime_header_decode($str->Subject) as $value) {

            $arr['Subject'] .= mb_convert_encoding($value->text, $value->charset , 'UTF-8');
        }
        return $arr;
    }

    /**
     * 删除邮件
     * @param int 邮件Id
     * @return bool 
     */
    public function DelMail($id)
    {
        //var_dump(imap_mailboxmsginfo($this->mail_obj));
        imap_delete($this->mail_obj, $id , 0);
        return imap_expunge($this->mail_obj);
    }

    /**
     * 获取邮件body编码
     * @param int 邮件id
     * @return string 邮件编码
     */
    private function Gcharset($id)
    {
        $str = imap_fetchstructure($this->mail_obj,$id);
        if(isset($str->parts)) {
            return $str->parts[0]->parameters[0]->value;
        }else {
            return $str->parameters[0]->value;
        }

    }

    /**
     * 判断数据越界
     * @param int 邮件id
     * @return bool 
     */
    private function GBumBool($num)
    {
        if($this->num == '')
        {
            $nu = $this->GmailNum();
            if($num > $nu)
            {
                echo '邮件ID越界';
                exit;
            }
        }elseif ($num > $this->num) {
            echo '邮件ID越界';
            exit;
        }
    }


}

