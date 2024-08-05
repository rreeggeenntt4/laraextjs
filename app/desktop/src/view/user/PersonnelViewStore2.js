Ext.define('extjs.view.user.PersonnelViewStore2', {
    extend: 'Ext.data.Store',
    alias: 'store.personnelviewstore2',
    fields: [
        'id', 'name', 'email', {
            name: 'education',
            mapping: 'educations[0].degree'
        }, {
            name: 'cities',
            convert: function(value, record) {
                var cities = record.get('cities');
                return cities ? cities.map(city => city.name).join(', ') : '';
            }
        }
    ],
    proxy: {
        type: 'ajax',
        url: 'http://127.0.0.1:8000/api/users',
        reader: {
            type: 'json',
            rootProperty: ''
        }
    },
    autoLoad: true
});

