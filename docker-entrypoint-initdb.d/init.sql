CREATE DATABASE email_sender;

\c email_sender


CREATE SEQUENCE emails_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."emails" (
    "id" integer DEFAULT nextval('emails_id_seq') NOT NULL,
    "from" character varying NOT NULL,
    "to" character varying NOT NULL,
    "subject" character varying NOT NULL,
    "body" text NOT NULL
) WITH (oids = false);


CREATE SEQUENCE users_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."users" (
    "id" integer DEFAULT nextval('users_id_seq') NOT NULL,
    "email" character varying NOT NULL,
    "password" character varying NOT NULL
) WITH (oids = false);