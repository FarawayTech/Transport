db.ch_stops.ensureIndex({ "location" : "2dsphere" });
db.ch_stops.ensureIndex({names:"text"}, {default_language:"none"});
db.ch_stops.ensureIndex({prefix_names:"text"}, {default_language:"none"});
