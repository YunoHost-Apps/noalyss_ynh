--
-- PostgreSQL database dump
-- Version 2007-09-08 02:49
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: parm_code; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY parm_code (p_code, p_value, p_comment) FROM stdin;
BANQUE	51	Poste comptable par défaut pour les banques
CAISSE	53	Poste comptable par défaut pour les caisses
CUSTOMER	410	Poste comptable par défaut pour les clients
VENTE	707	Poste comptable par défaut pour les ventes
VIREMENT_INTERNE	58	Poste comptable par défaut pour les virements internes
\.

--
-- PostgreSQL database dump complete
--
