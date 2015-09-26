--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';

SET search_path = public, pg_catalog;

delete from form where fo_fr_id=3000000;
delete from formdef where fr_id=3000000;

INSERT INTO formdef (fr_id, fr_label) VALUES (3000000, 'TVA déclaration');
--
-- Data for TOC entry 2 (OID 315304)
-- Name: formdef; Type: TABLE DATA; Schema: public; Owner: dany
--
