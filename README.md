#Kickstart-app

##What is it?

It's a skeleton application based upon `kickstart`. This is the "user-space" of the 
Kickstart project. This contains the files that the user should modify in order to
create their own, `kickstart`-based application.

##OK, so how do I install this thing?

You can use Composer to install the `kickstart-app` and then modify it to your needs.

One thing of notice here: replace `app-name` in the command below with whatever name
you want. `app-name` is the name of the folder which will be created in the current
working directory:

```
composer create-project noctis/kickstart-app app-name --repository='{"type":"vcs","url":"git@bitbucket.org:NoctisPL\/kickstart.git"}'
```