# Od
A Simple php API to get onedrive direct link from its sharelink.  

### Usage:  
 * Upload to your web server.  
 * Access the api via browser:
   ```
   http(s)://xxx/od.php?l=<shared short link>
   ```  
 * Through rewrite:  
   (nginx)  
   ```  
   rewrite ^/od/(.*)$ /od.php?l=$1?;
   ```  
   Then you just need to use:  
   ```
   http(s)://xxx/od/<shared short link>  
   ```  
   Moreover,I recommend you change the setting of **$finallink** to be like this in od.php:
   ```php
   $finallink = 'od/{link}'; /*设置生成后给出的链接，{link}是生成的链接*/  
   ```
   
### Get the shared short link:
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxfe9rzij20as05edfy)  
  
  First , have the file shared.  
  
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxgow1gsj209c083wek)  
  
  Second , set the permission to let everyone access.  
  
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxiyeylxj209y04eq2v)  
  
  Third , access *http(s)://xxx/od.php* and paste the link,click 'submit' to get shared short link.  
  
### Custom Link  
  * Change the setting of **$key** to your own key in md5 encryption.  
  ```php
  $key = '179ad45c6ce2cb97cf1029e212046e81'; /*(example)md5加密的key*/  
  ```
  * After pasting the onedrive link , type the key in the **Secretkey** inputbox while your custom link in another one.  
  * Enjoy your privilege！  
  
### Necessary doc
  * Please pay attention to the official limit -A- .  
    [https://docs.microsoft.com/zh-cn/sharepoint/dev/general-development/how-to-avoid-getting-throttled-or-blocked-in-sharepoint-online](https://docs.microsoft.com/zh-cn/sharepoint/dev/general-development/how-to-avoid-getting-throttled-or-blocked-in-sharepoint-online)  

### Have fun!  
  Let's be BAI PIAO GUAI.  
