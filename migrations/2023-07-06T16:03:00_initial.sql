
CREATE TABLE "implementations" (
  "id" integer,
  "language" text NOT NULL,
  "name" text NOT NULL DEFAULT '0',
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
CREATE UNIQUE INDEX "language_implementations" ON "implementations" ("language", "name");

CREATE TABLE "executions" (
    "id" integer,
    "implementation_id" integer NOT NULL,
    "example_id" integer NOT NULL,
    "self_duration" integer,
    "node_duration" integer,
    "meta" text,
    "result" integer,
    "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);