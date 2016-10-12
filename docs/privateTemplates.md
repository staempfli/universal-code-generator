# Create own Templates just for you

## Install project tool

0. For that you cannot use the `.phar` binary, so you need to install the project:

	```bash
	git clone https://github.com/staempfli/magento2-code-generator.git
	cd magento2-code-generator && composer install
	``` 

0. You can now use the comman in `bin` folder:

	* `bin/mg2-codegen` 


0. If you want to use the command globally on your system:

    `sudo ln -s $(PWD)/magento2-code-generator/bin/mg2-codegen /usr/local/bin/mg2-codegen`  
    
## Create Private Template

Do the same as in the Create Templates documentation but place your template into `privateTemplates` folder

* [How to create templates](createTemplates.md)
    
## Priority

Private templates have the highest priority. That means, that if you create a template with same name as an exiting one, your private will be used.