## Instructions
Folder content  will be replaced after the first cron run by the content based on `mypluginslug1.zip`.

## Configuration
1. Download `mypluginslug1.zip` from the folder above to your computer
2. Configure constants `PREFIX_DOMAIN` and `PREFIX_REPOFOLDER` in `mypluginslug1.php` so it meets your repository url (see line `57` and `114`)
3. Upload new `mypluginslug1.zip` to the folder above
4. Run cron, URL could look like `https://www.example.com/repositoryfolder/get-info.php?action=cron` alias `PREFIX_DOMAIN/PREFIX_REPOFOLDER/get-info.php?action=cron`

## Recomendation
I recommend you to disable jSon cache white dev mode is on, instrucions are on the lines `54` and `111`.