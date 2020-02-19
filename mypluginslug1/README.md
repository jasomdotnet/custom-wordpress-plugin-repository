## Instructions
The content of this folder will be replaced after the first cron run with the content based on `mypluginslug1.zip`

## Configuration
1. Download `mypluginslug1.zip` to your computer
2. Configure constants `PREFIX_DOMAIN` and `PREFIX_REPOFOLDER` so it meets your repository url (see line 57 and 114)
3. Upload new zip to repo folder
4. Run cron, URL could look like `https://www.example.com/repositoryfolder/get-info.php?action=cron` alias `PREFIX_DOMAIN/PREFIX_REPOFOLDER/get-info.php?action=cron`