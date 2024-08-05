Ext.define('extjs.view.user.PersonnelViewController2', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.personnelviewcontroller2',

    onEditComplete: function(editor, context) {
        var userId = context.record.get('id');
        var newDegree = context.record.get('education');

        Ext.Ajax.request({
            url: 'http://127.0.0.1:8000/api/users/' + userId + '/education',
            method: 'GET',
            params: {
                degree: newDegree
            },
            success: function(response) {
                Ext.Msg.alert('Success', 'Education updated successfully.');
                context.record.commit();  // Confirm changes in the grid
            },
            failure: function(response) {
                Ext.Msg.alert('Failure', 'Failed to update education.');
                context.record.reject();  // Revert changes in the grid
            }
        });
    }
});


