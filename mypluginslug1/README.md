## Instruction
Content of this folder will be replaced after first cron run with the content based on `mypluginslug1.zip`

## Configuration
1. Download .zip file to your computer
2. Configure constants `PREFIX_DOMAIN` and `PREFIX_REPOFOLDER` so it meet your repository url (see line 57 and 114)
3. Upload new zip to repo folder
4. Run cron, URL could looks like `https://www.example.com/repositoryfolder/get-info.php?action=cron` alias `PREFIX_DOMAIN/PREFIX_REPOFOLDER/get-info.php?action=cron`