# PHP基于IMAP/POP3协议一键获取邮件类库


#### 实例代码

```
$obj = new GetMail('pop.163.com','XXXX@163.com','TFNSWHMMOVMITCHD','pop3');


//获取邮件总条数 $id
$obj->GmailNum();

//获取邮件头信息
$obj->GMailTit($id)

//获取邮件主体信息 获取HTML信息
$obj->GMailBody($id,true);

//获取邮件主体信息 获取纯文本信息
$obj->GMailBody($id);

//删除邮件 
$obj->DelMail($id);
```
