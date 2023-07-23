
CREATE TABLE "implementation" (
  "id" integer,
  "language" string NOT NULL,
  "implementation" string NOT NULL DEFAULT '0',
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
CREATE UNIQUE INDEX "language_implementation" ON "implementation" ("language", "implementation");

CREATE TABLE "execution" (
    "id" integer,
    "implementation_id" integer NOT NULL,
    "example_id" integer NOT NULL,
    "self_duration" float,
    "node_duration" float,
    "meta" text,
    "result" integer,
    "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);