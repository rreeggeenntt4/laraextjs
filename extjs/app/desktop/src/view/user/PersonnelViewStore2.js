// Ext.define('extjs.view.user.PersonnelViewStore2', {
//     extend: 'Ext.data.Store',
//     alias: 'store.personnelviewstore2',
//     fields: [
//         'name', 'email', 'phone', 'dept'
//     ],
//     groupField: 'dept',
//     data: { items: [
//         { name: 'Jean Luc',   email: "jeanluc.picard@enterprise.com", phone: "555-111-1111", dept: "bridge" },
//         { name: 'ModernWorf', email: "worf.moghsson@enterprise.com",  phone: "555-222-2222", dept: "engine" },
//         { name: 'Deanna',     email: "deanna.troi@enterprise.com",    phone: "555-333-3333", dept: "bridge" },
//         { name: 'Data',       email: "mr.data@enterprise.com",        phone: "555-444-4444", dept: "bridge" }
//     ]},
//     proxy: {
//         type: 'memory',
//         reader: {
//             type: 'json',
//             rootProperty: 'items'
//         }
//     }
// });

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

