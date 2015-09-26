--
-- PostgreSQL database dump
-- Version 2007-09-08 01:10
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_country) FROM stdin;
1	comptes de capitaux	0	FR
101	Capital	1	FR
105	Ecarts de réévaluation	1	FR
1061	Réserve légale	1	FR
1063	Réserves statutaires ou contractuelles	1	FR
1064	Réserves réglementées	1	FR
1068	Autres réserves	1	FR
108	Compte de l'exploitant	1	FR
12	résultat de l'exercice (bénéfice ou perte)	1	FR
145	Amortissements dérogatoires	1	FR
146	Provision spéciale de réévaluation	1	FR
147	Plus-values réinvesties	1	FR
148	Autres provisions réglementées	1	FR
15	Provisions pour risques et charges	1	FR
16	emprunts et dettes assimilees	1	FR
2	comptes d'immobilisations	0	FR
20	immobilisations incorporelles	2	FR
201	Frais d'établissement	20	FR
206	Droit au bail	20	FR
207	Fonds commercial	20	FR
208	Autres immobilisations incorporelles	20	FR
21	immobilisations corporelles	2	FR
23	immobilisations en cours	2	FR
27	autres immobilisations financieres	2	FR
280	Amortissements des immobilisations incorporelles	2	FR
281	Amortissements des immobilisations corporelles	2	FR
290	Provisions pour dépréciation des immobilisations incorporelles	2	FR
291	Provisions pour dépréciation des immobilisations corporelles (même ventilation que celle du compte 21)	2	FR
297	Provisions pour dépréciation des autres immobilisations financières	2	FR
3	comptes de stocks et en cours	0	FR
31	matieres premières (et fournitures)	3	FR
32	autres approvisionnements	3	FR
33	en-cours de production de biens	3	FR
34	en-cours de production de services	3	FR
35	stocks de produits	3	FR
37	stocks de marchandises	3	FR
391	Provisions pour dépréciation des matières premières (et fournitures)	3	FR
392	Provisions pour dépréciation des autres approvisionnements	3	FR
393	Provisions pour dépréciation des en-cours de production de biens	3	FR
394	Provisions pour dépréciation des en-cours de production de services	3	FR
395	Provisions pour dépréciation des stocks de produits	3	FR
397	Provisions pour dépréciation des stocks de marchandises	3	FR
4	comptes de tiers	0	FR
400	Fournisseurs et Comptes rattachés	4	FR
409	Fournisseurs débiteurs	4	FR
410	Clients et Comptes rattachés	4	FR
419	Clients créditeurs	4	FR
421	Personnel - Rémunérations dues	4	FR
428	Personnel - Charges à payer et produits à recevoir	4	FR
43	Sécurité sociale et autres organismes sociaux	4	FR
444	Etat - Impôts sur les bénéfices	4	FR
445	Etat - Taxes sur le chiffre d'affaires	4	FR
447	Autres impôts, taxes et versements assimilés	4	FR
45	Groupe et associes	4	FR
455	Associés - Comptes courants	45	FR
46	Débiteurs divers et créditeurs divers	4	FR
47	comptes transitoires ou d'attente	4	FR
481	Charges à répartir sur plusieurs exercices	4	FR
486	Charges constatées d'avance	4	FR
487	Produits constatés d'avance	4	FR
491	Provisions pour dépréciation des comptes de clients	4	FR
496	Provisions pour dépréciation des comptes de débiteurs divers	4	FR
5	comptes financiers	0	FR
50	valeurs mobilières de placement	5	FR
51	banques, établissements financiers et assimilés	5	FR
53	Caisse	5	FR
54	régies d'avance et accréditifs	5	FR
58	virements internes	5	FR
590	Provisions pour dépréciation des valeurs mobilières de placement	5	FR
6	comptes de charges	0	FR
60	Achats (sauf 603)	6	FR
603	variations des stocks (approvisionnements et marchandises)	6	FR
61	autres charges externes - Services extérieurs	6	FR
62	autres charges externes - Autres services extérieurs	6	FR
63	Impôts, taxes et versements assimiles	6	FR
641	Rémunérations du personnel	6	FR
644	Rémunération du travail de l'exploitant	6	FR
645	Charges de sécurité sociale et de prévoyance	6	FR
646	Cotisations sociales personnelles de l'exploitant	6	FR
65	Autres charges de gestion courante	6	FR
66	Charges financières	6	FR
67	Charges exceptionnelles	6	FR
681	Dotations aux amortissements et aux provisions - Charges d'exploitation	6	FR
686	Dotations aux amortissements et aux provisions - Charges financières	6	FR
687	Dotations aux amortissements et aux provisions - Charges exceptionnelles	6	FR
691	Participation des salariés aux résultats	6	FR
695	Impôts sur les bénéfices	6	FR
697	Imposition forfaitaire annuelle des sociétés	6	FR
699	Produits - Reports en arrière des déficits	6	FR
7	comptes de produits	0	FR
701	Ventes de produits finis	7	FR
706	Prestations de services	7	FR
707	Ventes de marchandises	7	FR
708	Produits des activités annexes	7	FR
709	Rabais, remises et ristournes accordés par l'entreprise	7	FR
713	Variation des stocks (en-cours de production, produits)	7	FR
72	Production immobilisée	7	FR
73	Produits nets partiels sur opérations à long terme	7	FR
74	Subventions d'exploitation	7	FR
75	Autres produits de gestion courante	7	FR
753	Jetons de présence et rémunérations d'administrateurs, gérants,...	75	FR
754	Ristournes perçues des coopératives (provenant des excédents)	75	FR
755	Quotes-parts de résultat sur opérations faites en commun	75	FR
76	Produits financiers	7	FR
77	Produits exceptionnels	7	FR
781	Reprises sur amortissements et provisions (à inscrire dans les produits d'exploitation)	7	FR
786	Reprises sur provisions pour risques (à inscrire dans les produits financiers)	7	FR
787	Reprises sur provisions (à inscrire dans les produits exceptionnels)	7	FR
79	Transferts de charges	7	FR
8	Comptes spéciaux	0	FR
9	Comptes analytiques	0	FR
\.

--
-- PostgreSQL database dump complete
--
