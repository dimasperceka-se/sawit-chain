
/*
 * LOAD SCRIPTS
 * Usage:
 * Define function = myPrettyCode ()...
 * loadScript("js/my_lovely_script.js", myPrettyCode);
 */

var jsArray = {};

function loadScript(scriptName, callback) {

  if (!jsArray[scriptName]) {
    jsArray[scriptName] = true;

    // adding the script tag to the head as suggested before
    var body = document.getElementsByTagName('body')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = scriptName;

    // then bind the event to the callback function
    // there are several events for cross browser compatibility
    //script.onreadystatechange = callback;
    script.onload = callback;

    // fire the loading
    body.appendChild(script);

  } else if (callback) {// changed else to else if(callback)
    // console.log("JS file already added!");
    //execute function
    callback();
  }

}

/* ~ END: LOAD SCRIPTS */

Ext.util.Observable.observe(Ext.data.Connection, {
    requestexception: (conn, response, options) => {
      if (response.status == 401 && Ext.getCmp('frm-revoke-session-password') === undefined) {
        
        function unlock() {
            var passwd = Ext.getCmp('frm-revoke-session-password').getValue();
            Ext.Ajax.request({
                url: '/api/common/revoke',
                method:'POST',
                params: {
                    uid:ktv.ktv_session,
                    passwd:passwd
                },
                success: function(response){
                    win.close();
                }
            });
        }
            
        var win = Ext.create('Ext.Window',{
            modal:true,
            constraint:true,
            frame:true,
            title:'Session Expired',
            items:[
                {
                    xtype:'form',
                    padding: 10,
                    items:[
                        {
                            xtype:'panel',
                            flex:1,
                            height:80,
                            width:350,
                            html:'<div>'+ktv.ktv_fullname+', your session has expired, please submit your password below to revoke your session or click the logout button to sign in as a different user</div>'
                        },
                        {
                            xtype:'textfield',
                            width:350,
                            height:40,
                            fieldStyle:'text-align:center',
                            id:'frm-revoke-session-password',
                            inputType:'password',
                            allowBlank:false,
                            emptyText:'Enter your password',
                            listeners: {
                                specialkey: function(field, e){
                                    if (e.getKey() == e.ENTER) {
                                        unlock();
                                    }
                                }
                            }
                        }    
                    ]
                }
            ],
            buttonAlign:'center',
            buttons:[
                {
                    xtype:'button',
                    text:'Unlock',
                    handler: function() {
                        unlock();
                    }
                },
                {
                    xtype:'button',
                    text:'Logout',
                    handler: function() {
                        Ext.MessageBox.show({
                            title: 'Logout',
                            msg: 'Are you sure you want to logout?',
                            buttonText:{ 
                                yes: "Yes, I want to logout", 
                            },
                            fn: (btn) => {
                                console.log('Application Logout..');
                                if(btn === 'yes'){
                                    window.location = '/';
                                }
                            }
                        });
                    }
                }
            ]
        }).show();
      }
        
    },
     requestcomplete:(conn,response,options) => {
      // console.log('completes',response)
      
    }
});


/*
 * ExtJS MVC Application
 *
 */

 Ext.application({
     requires: [],
     name: 'Koltiva',
     appFolder: '/js/app',
     launch: function() {
         /* Nothin happened yaww... */

     }
 });
