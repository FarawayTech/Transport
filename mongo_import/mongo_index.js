db.stops.ensureIndex({ "location" : "2dsphere" });
db.stops.ensureIndex({names: 1});
db.stops.ensureIndex({prefix_names: 1});