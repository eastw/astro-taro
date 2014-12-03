$.fn.serializeObject = function() {
    var data = { };
    $.each( this.serializeArray(), function( key, obj ) {
        var a = obj.name.match(/(.*?)\[(.*?)\]/);
        if(a !== null)
        {
            var subName = new String(a[1]);
            var subKey = new String(a[2]);
            if( !data[subName] ) data[subName] = { };
            if( data[subName][subKey] ) {
                if( $.isArray( data[subName][subKey] ) ) {
                    data[subName][subKey].push( obj.value );
                } else {
                    data[subName][subKey] = { };
                    data[subName][subKey].push( obj.value );
                };
            } else {
                data[subName][subKey] = obj.value;
            };  
        } else {
            var keyName = new String(obj.name);
            if( data[keyName] ) {
                if( $.isArray( data[keyName] ) ) {
                    data[keyName].push( obj.value );
                } else {
                    data[keyName] = { };
                    data[keyName].push( obj.value );
                };
            } else {
                data[keyName] = obj.value;
            };
        };
    });
    return data;
};