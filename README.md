# Od
A Simple php API to get onedrive direct link from its sharelink.  

### Usage:  
 * Upload to your web server.  
 * Access the api via browser with parameter:
   ```
   http(s)://xxx/od.php?l=<onedrive share link in base64 encoding>
   ```  
 * Through rewrite:  
   (nginx)  
   ```  
   rewrite ^/od/(.*)$ /od.php?l=$1?;
   ```  
   Then you just need to use:  
   ```
   http(s)://xxx/od/<onedrive share link in base64 encoding>  
   ```  
   
### Get the sharelink:
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxfe9rzij20as05edfy)  
  
  First , have the file shared.  
  
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxgow1gsj209c083wek)  
  
  Second , set the permission to let everyone access.  
  
  ![](https://wx4.sinaimg.cn/large/ed039e1fly1g5pxiyeylxj209y04eq2v)  
  
  Third , access *http(s)://xxx/od.php* and paste the link to get base64 encoded link.  

### Have fun!  
  Let's be BAI PIAO GUAI.  
