Ext.define('extjs.view.user.PersonnelView2', {
    extend: 'Ext.grid.Grid',
    xtype: 'personnelview2',
    cls: 'personnelview2',
    requires: ['Ext.grid.rowedit.Plugin'],
    controller: {type: 'personnelviewcontroller2'},
    viewModel: {type: 'personnelviewmodel2'},
    store: {type: 'personnelviewstore2'},
    grouped: true,
    groupFooter: {
        xtype: 'gridsummaryrow'
    },
    plugins: {
        gridfilters: true,
        rowedit: {
            autoConfirm: false
        }
    },
    columns: [
        {
            text: 'ID',
            dataIndex: 'id',
            width: 50
        },
        {
            text: 'Name',
            dataIndex: 'name',
            editable: true,
            width: 100,
            cell: {userCls: 'bold'}
        },
        {text: 'Email', dataIndex: 'email', editable: true, width: 230},
        {
            text: 'Education',
            dataIndex: 'education',
            editable: true,
            width: 150
        },
        {
            text: 'Cities',
            dataIndex: 'cities',
            width: 150,
            filter: 'string'
        }
    ],
    listeners: {
        edit: 'onEditComplete'
    }
});
