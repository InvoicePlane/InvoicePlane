# How to: Adding e-Invoicing XML-templates

To implement a new e-invoicing xml template, there are 2 files that need to be placed in their respective folder.
Add the configuration file ("Shortidv10.php") in the folder "helpers/XMLconfigs/" and the xml-template file ("Shortidv10Xml.php") in the folder "libraries/XMLtemplates".
It is important to make the file names as short as possible and preferably use only numbers and letters. 
Each country has its format specifications and version on which it is best to base the shortened name. 

E.g. for Germany, the most commonly used format for B2B is Zugferd. 
The version number of the Zugferd template used in IP is version 1.0. 
So the abbreviated name for this xml template will be "Zugferdv10". 
- Add ".php" to the configuration file and "Xml.php" to the xml template file. 
- Then copy "Zugferdv10.php" into the "helpers/XMLconfigs" folder and "Zugferdv10Xml.php" into the "libraries/XMLtemplate" folder.

The configuration file contains explanations of the config items. 
In the included examples (Ublexamv20Xml.php and Zugferdv10Xml.php) you can find information that can help you create the proper XML template. 
Specific explanations about the correct e-invoicing format for a particular country (of your customer) can be found online on a government website, in principle.
