.. --initial-header-level=2

L2TP/IPsec
==========

This type of VPN is often available in almost all Android, 
iOS and Windows devices.

Enable L2TP
   When enabling L2TP, the server must be configured as 
   *Domain controller* inside the :guilabel:`Windows network`
    configuration page.
    Otherwise the authentication will fail.

IPsec authentication
   Select the authentication type.
   If you can't import a certificate into the client,
   you should use a PSK, even if less secure.

   * RSA: authentication based on certificates (see :guilabel:`Account` tab)
   * PSK (Pre-Shared Key): authentication based on secret key shared
     between server and client.
     This kind of key should be as strong as a good password.

Network address
   Network of L2TP clients. Eg: 192.168.78.0

Network mask
   Network mask of L2TP clients. Eg: 255.255.255.0

