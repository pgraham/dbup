CREATE TABLE my_table(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT
);

CREATE TABLE my_table_link(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	my_table_id INTEGER NOT NULL REFERENCES my_table(id),
	link_val TEXT
);

insertId := INSERT INTO my_table (name) VALUES ('aName');
linkId := INSERT INTO my_table_link (my_table_id, link_val)
	VALUES (:insertId, 'linked_value');
