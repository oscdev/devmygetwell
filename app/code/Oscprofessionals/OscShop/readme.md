 oscShop Extension for Magento® 2
 Magento 2 module version v1.1.1

 This extension will generate APK for Native Android Mobile App for Magento®2 frontend store.

 Version 1.1.1
 
 Extension Features:
  - Delete unwanted Countries
  - Add/remove/modify Region/State
  - Request for APK
  - Paypal Payment Method Configuration for Mobile App
  - Allowed Free Shipping based on minimum amount added from shipping method

 Mobile App features:
  - Lightning fast
  - Native Android Mobile App for Magento® 2 eStore
  - Share Products via Facebook
  - Auto fill new address through GPS
  - Slider for Home page banner
  - Payment Gateway – Bank Transfer, Cash On Delivery, Paypal Payment
  - Support Simple products only
  - All Magneto® default shipping methods supported

 Compatibility:
  - Magento® CE: 2.0.1 to 2.1.3
  - Android: 4.0(Ice Cream Sandwich) and above

 INSTALLATION:
 1. Unzip the oscShop extension for Magento 2.0.
 2. Upload the 'app' folder to your Magento directory.
 3. Run upgrade and compile command.
 4. Login to the Magento Admin as an administrator.
 5. Generate API Token Key for API Authorization - go to [ SYSTEM > Integration > Add New Integration ].
 6. Adjust the oscShop settings to configure connection - go to the [ OSCSHOP > Token Settings ] menu in the Magento admin panel.
 7. Add the Paypal Payment settings and credentials - go to the [ STORES > Configuration > Sales > Payment Methods > OSCPSHOP Payment Method ] menu in the Magento admin panel.
 8. oscShop is ready to use!
 
 To obtain PAYPAL API Client ID and Secret Key:
 1. If you have created an account with PayPal, login, else Signup to https://www.paypal.com/us/home
 2. Go to PayPal Developers and login. (https://developer.paypal.com/)
 3. Scroll down and click on Create App button under REST API apps.
 4. Provide required information, and click on Create App button.
 5. Once app is created, click on the app.
 6. Here you will see your Client ID and Secret Key, copy and paste it into Admin Paypal Configuration Section.
 [ STORES > Configuration > Sales > Payment Methods > OSCPSHOP Payment Method ]
 
 Steps need to follow to add the Banner Image:
 1. Image name should be:
    banner1.png
    banner2.png
    banner3.png
    banner4.png
 2. We can add maximum 4 images.  Not more than 4 images.
 3. Image Size should be 720*450 (for banner)
 4. Upload image in pub/media/banner through FTP
    Ex. {SITEURL}/pub/media/banner