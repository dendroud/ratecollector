--
-- PostgreSQL database dump
--

-- Dumped from database version 14.5 (Debian 14.5-1.pgdg110+1)
-- Dumped by pg_dump version 14.5 (Debian 14.5-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: rate; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.rate (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    value real,
    created timestamp with time zone DEFAULT now(),
    provider character varying(20),
    cur1 character(3),
    cur2 character(3)
);


ALTER TABLE public.rate OWNER TO admin;

--
-- Data for Name: rate; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.rate (id, value, created, provider, cur1, cur2) FROM stdin;
\.


--
-- Name: rate rate_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.rate
    ADD CONSTRAINT rate_pkey PRIMARY KEY (id);


--
-- Name: Rates; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX "Rates" ON public.rate USING btree (value);


--
-- Name: INDEX "Rates"; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON INDEX public."Rates" IS 'Index for rates';


--
-- Name: ind_created; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ind_created ON public.rate USING btree (created);


--
-- Name: ind_curname_provider; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ind_curname_provider ON public.rate USING btree (provider, cur1, cur2);


--
-- Name: INDEX ind_curname_provider; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON INDEX public.ind_curname_provider IS 'Index for currency names and provider';


--
-- Name: ind_curnames; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ind_curnames ON public.rate USING btree (cur1, cur2);


--
-- Name: INDEX ind_curnames; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON INDEX public.ind_curnames IS 'Index for currencies pairs';


--
-- PostgreSQL database dump complete
--

