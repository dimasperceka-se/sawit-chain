#### Git global setup
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email.address@koltiva.com"
```

#### Create a new repository
```bash
git clone git@gitrepo.koltiva.com:web-devel/PalmoilTrace.git
cd PalmoilTrace
touch README.md
git add README.md
git commit -m "add README"
git push -u origin master
```

#### Existing folder
```bash
cd existing_folder
git init
git remote add origin git@gitrepo.koltiva.com:web-devel/PalmoilTrace.git
git add .
git commit -m "Initial commit"
git push -u origin master
```

#### Existing Git repository
```bash
cd existing_repo
git remote rename origin old-origin
git remote add origin git@gitrepo.koltiva.com:web-devel/PalmoilTrace.git
git push -u origin --all
git push -u origin --tags
```