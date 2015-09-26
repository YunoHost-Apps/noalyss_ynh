--
-- PostgreSQL database dump
-- Version 2007-09-08 01:18
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_country) FROM stdin;
1	CLASSE 1. Comptes de capitaux 	0	FR
10	FONDS ASSOCIATIF ET RESERVES (pour les fondations : "fonds propres et réserves")	1	FR
102	Fonds associatif sans droit de reprise	10	FR
1021	Valeur du patrimoine intégré	102	FR
1022	Fonds statutaire (à éclater en fonction des statuts)	102	FR
1023	Subventions d'investissement non renouvelables	102	FR
1024	Apports sans droit de reprise	102	FR
1025	Legs et donations avec contrepartie d'actifs immobilisés	102	FR
1026	Subventions d'investissement affectées à des biens renouvelables	102	FR
103	Fonds associatif avec droit de reprise	10	FR
1031	Valeur des biens affectés repris à la fin du contrat d'apport	103	FR
1032	Valeur des biens affectés repris à la dissolution de l'association	103	FR
1033	Valeur des biens non affectés repris à la fin du contrat d'apport	103	FR
1034	Apports avec droit de reprise	103	FR
1035	Legs et donations avec contrepartie d'actifs immobilisés assortis d'une obligation ou d'une condition	103	FR
1036	Subventions d'investissement affectées à des biens renouvelables	103	FR
1039	Fonds associatif avec doit de reprise inscrit au compte de résultat	103	FR
105	Ecarts de réévaluation	10	FR
1051	Ecarts de réévaluation sur des biens sans droit de reprise	105	FR
1052	Ecarts de réévaluation (immobilisations non grevées d'un droit de reprise)	105	FR
1053	Ecarts de réévaluation (immobilisations grevées d'un droit de reprise)	105	FR
10531	Ecarts de réévaluation (immobilisations grevées d'un droit de reprise avant dissolution de l'association)	1053	FR
10532	Ecarts de réévaluation (immobilisations grevées d'un droit de reprise à la dissolution de l'association)	1053	FR
106	Réserves	10	FR
1062	Réserves indisponibles	106	FR
1063	Réserves statutaires ou contractuelles	106	FR
1064	Réserves réglementées	106	FR
1068	Autres réserves (dont réserves pour projet associatif)	106	FR
10682	Réserves pour investissements	1068	FR
10683	Réserves de trésorerie (provenant du résultat)	1068	FR
10688	Réserves diverses	1068	FR
11	ELEMENTS EN INSTANCE D'AFFECTATION	1	FR
110	Report à nouveau (solde créditeur)	11	FR
115	Résultats sous contrôle de tiers financeurs	11	FR
119	Report à nouveau (solde débiteur) 	11	FR
12	RESULTAT NET DE L'EXERCICE	1	FR
120	Résultat de l'exercice (excédent)	12	FR
129	Résultat de l'exercice (déficit)	12	FR
13	SUBVENTIONS D'INVESTISSEMENT (affectés à des biens renouvelables)	1	FR
131	Subventions d'investissement (renouvelables)	13	FR
139	Subventions d'investissement inscrites au compte de résultat	13	FR
15	PROVISIONS POUR RISQUES ET CHARGES	1	FR
151	Provisions pour risques	15	FR
1516	Provisions pour risques d'emploi	151	FR
1518	Autres provisions pour risques	151	FR
157	Provisions pour charges à répartir sur plusieurs exercices	15	FR
1572	Provisions pour grosses réparations	157	FR
16	EMPRUNTS ET DETTES ASSIMILEES	1	FR
164	Emprunts auprès des établissements de crédit	16	FR
1641	Emprunts (à détailler)	164	FR
167	Emprunts et dettes assorties de conditions particulières	16	FR
1672	Titres associatifs	167	FR
168	Autres emprunts et dettes assimilées	16	FR
1681	Autres emprunts (à détailler)	168	FR
1685	Rentes viagères capitalisées	168	FR
1687	Autres dettes (à détailler)	168	FR
1688	Intérêts courus (à détailler)	168	FR
18	COMPTES DE LIAISON DES ETABLISSEMENTS	1	FR
181	Apports permanents entre siège social et établissements	18	FR
185	Biens et prestations de services échangés entre établissements et siège social	18	FR
186	Biens et prestations de services échangés entre établissements (charges)	18	FR
187	Biens et prestations de services échangés entre établissements (produits)	18	FR
19	FONDS DEDIES	1	FR
194	Fonds dédiés sur subventions de fonctionnement	19	FR
195	Fonds dédiés sur dons manuels affectés	19	FR
197	Fonds dédiés sur legs et donations affectés	19	FR
198	Excédent disponible après affectation au projet associatif	19	FR
199	Reprise des fonds affectés au projet associatif	19	FR
2	CLASSE 2. Comptes d'immobilisations 	0	FR
20	IMMOBILISATIONS INCORPORELLES	2	FR
201	Frais d'établissement	20	FR
2012	Frais de premier établissement	201	FR
206	Droit au bail	20	FR
208	Autres immobilisations incorporelles	20	FR
21	IMMOBILISATIONS CORPORELLES	2	FR
211	Terrains	21	FR
212	Agencements et aménagements des constructions	21	FR
213	Constructions	21	FR
2131	Bâtiments	213	FR
2135	Installations générales, agencements, aménagements des constructions	213	FR
214	Constructions sur sol d'autrui	21	FR
215	Installations techniques, matériel et outillage industriels	21	FR
2151	Installations complexes spécialisées	215	FR
2154	Matériel industriel	215	FR
2155	Outillage industriel	215	FR
218	Autres immobilisations corporelles	21	FR
2181	Installations générales, agencements, aménagements divers	218	FR
2182	Matériel de transport	218	FR
2183	Matériel de bureau et matériel informatique	218	FR
2184	Mobilier	218	FR
2185	Cheptel	218	FR
228	Immobilisations grevées de droits	2	FR
229	Droits des propriétaires	2	FR
23	IMMOBILISATIONS EN COURS	2	FR
231	Immobilisations corporelles en cours	23	FR
2313	Constructions	231	FR
2315	Installations techniques, matériel et outillage industriels	231	FR
2318	Autres immobilisations corporelles	231	FR
238	Avances et acomptes versés sur commande d'immobilisations corporelles	23	FR
26	PARTICIPATIONS ET CREANCES RATTACHEES A DES PARTICIPATIONS	2	FR
261	Titres de participation	26	FR
266	Autres formes de participation	26	FR
267	Créances rattachées à des participations	26	FR
269	Versements restant à effectuer sur titres de participation non libérés	26	FR
27	AUTRES IMMOBILISATIONS FINANCIERES	2	FR
271	Titres immobilisés (droit de propriété)	27	FR
2711	Actions	271	FR
272	Titres immobilisés (droit de créance)	27	FR
2721	Obligations	272	FR
2722	Bons	272	FR
2728	Autres	272	FR
274	Prêts	27	FR
2743	Prêts au personnel	274	FR
2748	Autres prêts	274	FR
275	Dépôts et cautionnements versés	27	FR
2751	Dépôts	275	FR
2755	Cautionnements	275	FR
276	Autres créances immobilisées	27	FR
2761	Créances diverses	276	FR
2768	Intérêts courus (à détailler)	276	FR
279	Versement restant à effectuer sur titres immobilisés non libérés	27	FR
28	AMORTISSEMENTS DES IMMOBILISATIONS	2	FR
280	Amortissements des immobilisations incorporelles	28	FR
2801	Frais d'établissement (même ventilation que celle du compte 201)	280	FR
2808	Autres immobilisations incorporelles	280	FR
281	Amortissements des immobilisations corporelles	28	FR
2812	Agencements, aménagements de terrains (même ventilation que celle du compte 212)	281	FR
2813	Constructions (même ventilation que celle du compte 213)	281	FR
2814	Constructions sur sol d'autrui (même ventilation que celle du compte 214)	281	FR
2815	Installations techniques, matériel et outillage industriels (même ventilation que celle du compte 215)	281	FR
2818	Autres immobilisations corporelles (même ventilation que celle du compte 218)	281	FR
29	PROVISIONS POUR DEPRECIATION DES IMMOBILISATIONS	2	FR
290	Provisions pour dépréciation des immobilisations incorporelles	29	FR
2906	Droit au bail	290	FR
2908	Autres immobilisations incorporelles	290	FR
291	Provisions pour dépréciation des autres immobilisations corporelles	29	FR
2911	Terrains	291	FR
296	Provisions pour dépréciation des participations et créances rattachées à des participations	29	FR
2961	Titres de participation	296	FR
2966	Autres formes de participation	296	FR
2967	Créances rattachées à des participations (même ventilation que celle du compte 267)	296	FR
297	Provisions pour dépréciation des autres immobilisations financières	29	FR
2971	Titres immobilisés (droit de propriété) (même ventilation que celle du compte 271)	297	FR
2972	Titres immobilisés (droit de créance) (même ventilation que celle du compte 272)	297	FR
2974	Prêts (même ventilation que celle du compte 274)	297	FR
2975	Dépôts et cautionnements versés (même ventilation que celle du compte 275)	297	FR
2976	Autres créances immobilisées (même ventilation que celle du compte 276)	297	FR
3	CLASSE 3. Comptes de stocks et en-cours 	0	FR
31	MATIERES PREMIERES ET FOURNITURES	3	FR
32	AUTRES APPROVISIONNEMENTS	3	FR
33	EN-COURS DE PRODUCTION DE BIENS	3	FR
34	EN-COURS DE PRODUCTION DE SERVICES	3	FR
35	STOCKS DE PRODUITS	3	FR
37	STOCKS DE MARCHANDISES	3	FR
39	PROVISIONS POUR DEPRECIATION DES STOCKS ET EN-COURS	3	FR
391	Provisions pour dépréciation des matières premières et fournitures	39	FR
392	Provisions pour dépréciation des autres approvisionnements	39	FR
393	Provisions pour dépréciation des en-cours de production de biens	39	FR
394	Provisions pour dépréciation des en-cours de production de services	39	FR
395	Provisions pour dépréciation des stocks de produits	39	FR
397	Provisions pour dépréciation des stocks de marchandises	39	FR
4	CLASSE 4. Comptes de tiers 	0	FR
40	FOURNISSEURS ET COMPTES RATTACHES	4	FR
401	Fournisseurs	40	FR
4011	Fournisseurs - Achats de biens ou de prestations de services	401	FR
404	Fournisseurs d'immobilisations	40	FR
4041	Fournisseurs - achats d'immobilisations	404	FR
4047	Fournisseurs d'immobilisations - Retenues de garantie	404	FR
408	Fournisseurs - Factures non parvenues	40	FR
4081	Fournisseurs - Achats de biens ou de prestations de services	408	FR
4084	Fournisseurs - achats d'immobilisations	408	FR
409	Fournisseurs débiteurs	40	FR
4091	Fournisseurs - Avances et acomptes versés sur commandes	409	FR
4096	Fournisseurs - Créances pour emballage et matériel à rendre	409	FR
41	USAGERS ET COMPTES RATTACHES	4	FR
411	Usagers (et organismes de prise en charge)	41	FR
416	Créances douteuses ou litigieuses	41	FR
418	Usagers - Produits non encore facturés	41	FR
419	Usagers créditeurs	41	FR
42	PERSONNEL ET COMPTES RATTACHES	4	FR
421	Personnel - Rémunérations dues	42	FR
422	Comités d'entreprises, d'établissement...	42	FR
425	Personnel - Avances et acomptes	42	FR
427	Personnel - Oppositions	42	FR
428	Personnel - Charges à payer et produits à recevoir	42	FR
4282	Dettes provisionnées pour congés à payer	428	FR
4286	Autres charges à payer	428	FR
4287	Produits à recevoir	428	FR
43	SECURITE SOCIALE ET AUTRES ORGANISMES SOCIAUX	4	FR
431	Sécurité sociale	43	FR
437	Autres organismes sociaux	43	FR
4372	Mutuelles	437	FR
4373	Caisses de retraites et de prévoyance	437	FR
4374	Caisses d'allocations de chômage - ASSEDIC	437	FR
4378	Autres organismes sociaux - divers	437	FR
438	Organismes sociaux - Charges à payer et produits à recevoir	43	FR
4382	Charges sociales sur congés à payer	438	FR
4386	Autres charges à payer	438	FR
4387	Produits à recevoir	438	FR
44	ETAT ET AUTRES COLLECTIVITES PUBLIQUES	4	FR
441	Etat - Subventions à recevoir	44	FR
4411	Subventions d'investissement	441	FR
4417	Subventions d'exploitation	441	FR
4419	Avances sur subventions	441	FR
444	Etat - Impôts sur les bénéfices	44	FR
4445	Etat - Impôt sur les société (organismes sans but lucratif)	444	FR
445	Etat - Taxes sur le chiffre d'affaires	44	FR
447	Autres impôts, taxes et versements assimilés	44	FR
4471	Impôts, taxes et versements assimilés sur rémunérations (administration des impôts)	447	FR
44711	Taxe sur les salaires	4471	FR
44713	Participation des employeurs à la formation professionnelle continue	4471	FR
44714	Cotisation pour défaut d'investissement obligatoire dans la construction	4471	FR
44718	Autres impôts, taxes et versements assimilés	4471	FR
4473	Impôts, taxes et versements assimilés sur rémunérations (autres organismes)	447	FR
44733	Participation des employeurs à la formation professionnelle continue	4473	FR
44734	Participation des employeurs à l'effort de construction (versements à fonds perdus)	4473	FR
4475	Autres impôts, taxes et versements assimilés (administration des impôts)	447	FR
4477	Autres impôts, taxes et versements assimilés (autres organismes)	447	FR
448	Etat - Charges à payer et produits à recevoir	44	FR
4482	Charges fiscales sur congés à payer	448	FR
4486	Autres charges à payer	448	FR
4487	Produits à recevoir	448	FR
45	CONFEDERATION, FEDERATION, UNION, ASSOCIATIONS AFFILIEES ET SOCIETAIRES 	4	FR
451	Confédération, fédération, union et associations affiliées - Compte courant	45	FR
455	Sociétaires - Comptes courants	45	FR
46	DEBITEURS DIVERS ET CREDITEURS DIVERS	4	FR
467	Autres comptes débiteurs ou créditeurs	46	FR
468	Divers - Charges à payer et produits à recevoir	46	FR
4686	Charges à payer	468	FR
4687	Produits à recevoir	468	FR
47	COMPTES D'ATTENTE	4	FR
471	Recettes à classer	47	FR
472	Dépenses à classer et à régulariser	47	FR
475	Legs et donations en cours de réalisation	47	FR
48	COMPTE DE REGULARISATION	4	FR
481	Charges à répartir sur plusieurs exercices	48	FR
4812	Frais d'acquisition des immobilisations	481	FR
4818	Charges à étaler	481	FR
486	Charges constatées d'avance	48	FR
487	Produits constatés d'avance	48	FR
49	PROVISIONS POUR DEPRECIATION DES COMPTES DE TIERS	4	FR
491	Provisions pour dépréciation des comptes d'usagers (et organismes de prise en charge)	49	FR
496	Provisions pour dépréciation des comptes de débiteurs divers	49	FR
5	CLASSE 5. Comptes financiers 	0	FR
50	VALEURS MOBILIERES DE PLACEMENT	5	FR
503	Actions	50	FR
5031	Titres cotés	503	FR
5035	Titres non cotés	503	FR
506	Obligations	50	FR
5061	Titres cotés	506	FR
5065	Titres non cotés	506	FR
507	Bons du Trésor et bons de caisse à court terme	50	FR
508	Autres valeurs mobilières et créances assimilées	50	FR
5081	Autres valeurs mobilières	508	FR
5088	Intérêts courus sur obligations, bons et valeurs assimilées	508	FR
51	BANQUES, ETABLISSEMENTS FINANCIERS ET ASSIMILES	5	FR
512	Banques	51	FR
514	Chèques postaux	51	FR
515	Caisses	51	FR
517	Autres organismes financiers	51	FR
5171	Caisse d'épargne	517	FR
518	Intérêts courus	51	FR
5186	Intérêts courus à payer	518	FR
5187	Intérêts courus à recevoir	518	FR
53	CAISSE	5	FR
531	Caisse du siège	53	FR
532	Caisses des lieux d'activités	53	FR
54	REGIES D'AVANCES ET ACCREDITIFS	5	FR
541	Régies d'avances	54	FR
542	Accréditifs	54	FR
58	VIREMENTS INTERNES	5	FR
581	Virements de fonds	58	FR
59	PROVISIONS POUR DEPRECIATION DES COMPTES FINANCIERS	5	FR
590	Provisions pour dépréciation des valeurs mobilières de placement	59	FR
6	CLASSE 6. Comptes de charges 	0	FR
60	ACHATS (sauf 603)	6	FR
601	Achats stockés - Matières premières et fournitures (*1 Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif)	60	FR
602	Achats stockés - Autres approvisionnements (*1 Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif)	60	FR
604	Achats d'études et prestations de services (*2 Incorporés directement aux produits et prestations de services)	60	FR
606	Achats non stockés de matières et fournitures (*1 Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif)	60	FR
6061	Fournitures non stockables (eau, énergie...)	606	FR
6063	Fournitures d'entretien et de petit équipement	606	FR
6064	Fournitures administratives	606	FR
6068	Autres matières et fournitures	606	FR
607	Achats de marchandises	60	FR
6071	Marchandise A	607	FR
6072	Marchandise B	607	FR
609	Rabais, remises et ristournes obtenues sur achats	60	FR
603	Variation des stocks (approvisionnements et marchandises)	6	FR
6031	Variation des stocks de matières premières et fournitures	6	FR
6032	Variation des stocks des autres approvisionnements	6	FR
6037	Variation des stocks de marchandises	6	FR
61	AUTRES CHARGES EXTERNES - Services extérieurs	6	FR
611	Sous-traitance générale	61	FR
612	Redevances de crédit-bail	61	FR
6122	Crédit-bail mobilier	612	FR
613	Locations	61	FR
6132	Locations immobilières	613	FR
6135	Locations mobilières	613	FR
614	Charges locatives et de co-propriété	61	FR
615	Entretien et réparations	61	FR
6152	... sur biens immobiliers	615	FR
6155	... sur biens mobiliers	615	FR
6156	Maintenance	615	FR
616	Primes d'assurances	61	FR
6161	Multirisques	616	FR
6162	Assurance obligatoire dommage-construction	616	FR
6168	Autres assurances	616	FR
617	Etudes et recherches	61	FR
618	Divers	61	FR
6181	Documentation générale	618	FR
6183	Documentation technique	618	FR
6185	Frais de colloques, séminaires, conférences	618	FR
619	Rabais, remises et ristournes obtenues sur services extérieurs	61	FR
62	AUTRES CHARGES EXTERNES - AUTRES SERVICES EXTERIEURS	6	FR
621	Personnel extérieur à l'association	62	FR
622	Rémunérations d'intermédiaires et honoraires	62	FR
6226	Honoraires	622	FR
6227	Frais d'actes et de contentieux	622	FR
623	Publicité, publications, relations publiques	62	FR
6231	Annonces et insertions	623	FR
6233	Foires et expositions	623	FR
6236	Catalogues et imprimés	623	FR
6237	Publications	623	FR
6238	Divers (pourboires, dons courants...)	623	FR
624	Transports de biens et transports collectifs du personnel	62	FR
6241	Transports sur achats	624	FR
6243	Transports entre établissements	624	FR
6247	Transports collectifs du personnel	624	FR
6248	Divers	624	FR
625	Déplacements, missions et réceptions	62	FR
6251	Voyages et déplacements	625	FR
6256	Missions	625	FR
6257	Réceptions	625	FR
626	Frais postaux et frais de télécommunications	62	FR
627	Services bancaires et assimilés	62	FR
628	Divers	62	FR
6281	Cotisations (liées à l'activité économique)	628	FR
6284	Frais de recrutement du personnel	628	FR
629	Rabais, remises et ristournes obtenus sur autres services extérieurs	62	FR
63	IMPOTS, TAXES ET VERSEMENTS ASSIMILES	6	FR
631	Impôts, taxes et versements assimilés sur rémunérations (administration des impôts)	63	FR
6311	Taxe sur les salaires	631	FR
6313	Participation des employeurs à la formation professionnelle continue	631	FR
6314	Cotisation pour défaut d'investissement obligatoire dans la construction	631	FR
633	Impôts, taxes et versements assimilés sur rémunérations (autres organismes)	63	FR
6331	Versement de transport	633	FR
6333	Participation des employeurs à la formation professionnelle continue	633	FR
6334	Participation des employeurs à l'effort de construction (versements à fonds perdus)	633	FR
635	Autres impôts, taxes et versements assimilés (administration des impôts)	63	FR
6351	Impôts directs	635	FR
63512	Taxes foncières	6351	FR
63513	Autres impôts locaux	6351	FR
63518	Autres impôts directs	6351	FR
6353	Impôts indirects	635	FR
6354	Droits d'enregistrement et de timbre	635	FR
6358	Autres droits	635	FR
637	Autres impôts, taxes et versements assimilés (autres organismes)	63	FR
64	CHARGES DE PERSONNEL	6	FR
641	Rémunérations du personnel	64	FR
6411	Salaires, appointements	641	FR
6412	Congés payés	641	FR
6413	Primes et gratifications	641	FR
6414	Indemnités et avantages divers	641	FR
6415	Supplément familial	641	FR
645	Charges de sécurité sociale et de prévoyance	64	FR
6451	Cotisations à l'URSSAF	645	FR
6452	Cotisations aux mutuelles	645	FR
6453	Cotisations aux caisses de retraites et de prévoyance	645	FR
6454	Cotisations aux ASSEDIC	645	FR
6458	Cotisations aux autres organismes sociaux	645	FR
647	Autres charges sociales	64	FR
6472	Versements aux comités d'entreprise et d'établissement	647	FR
6475	Médecine du travail, pharmacie	647	FR
648	Autres charges de personnel	64	FR
65	AUTRES CHARGES DE GESTION COURANTE	6	FR
651	Redevances pour concessions, brevets, licences, marques, procédés, droits et valeurs similaires	65	FR
6511	Redevances pour concessions, brevets, licences, marques, procédés	651	FR
6516	Droits d'auteur et de reproduction (SACEM)	651	FR
6518	Autres droits et valeurs similaires	651	FR
654	Pertes sur créances irrécouvrables	65	FR
6541	Créances de l'exercice	654	FR
6544	Créances des exercices antérieurs	654	FR
657	Subventions versées par l'association	65	FR
6571	Bourses accordées aux usagers	657	FR
658	Charges diverses de gestion courante	65	FR
6586	Cotisations (liées à la vie statutaire)	658	FR
66	CHARGES FINANCIERES	6	FR
661	Charges d'intérêts	66	FR
6611	Intérêts des emprunts et dettes	661	FR
6616	Intérêts bancaires	661	FR
6618	Intérêts des autres dettes	661	FR
666	Pertes de change	66	FR
667	Charges nettes sur cessions de valeurs mobilières de placement	66	FR
67	CHARGES EXCEPTIONNELLES	6	FR
671	Charges exceptionnelles sur opérations de gestion	67	FR
6712	Pénalités et amendes fiscales ou pénales	671	FR
6713	Dons, libéralités	671	FR
6714	Créances devenues irrécouvrables dans l'exercice	671	FR
6717	Rappels d'impôts (autres qu'impôts sur les bénéfices)	671	FR
6718	Autres charges exceptionnelles sur opérations de gestion	671	FR
672	Charges sur exercices antérieurs (à reclasser)	67	FR
675	Valeurs comptables des éléments d'actif cédés	67	FR
6751	Immobilisations incorporelles	675	FR
6752	Immobilisations corporelles	675	FR
6756	Immobilisations financières	675	FR
678	Autres charges exceptionnelles 	67	FR
68	DOTATIONS AUX AMORTISSEMENTS, PROVISIONS ET ENGAGEMENTS	6	FR
681	Dotations aux amortissements, provisions et engagements	68	FR
6811	Dotations aux amortissements des immobilisations incorporelles et corporelles	681	FR
68111	Dotations aux amortissements des immobilisations incorporelles	6811	FR
68112	Dotations aux amortissements des immobilisations corporelles	6811	FR
6812	Dotations aux amortissements des charges d'exploitation à répartir	681	FR
6815	Dotations aux provisions pour risques et charges d'exploitation	681	FR
6816	Dotations aux provisions pour dépréciation des immobilisations incorporelles et corporelles	681	FR
6817	Dotations aux provisions pour dépréciation des actifs circulants (autres que les valeurs mobilières de placement)	681	FR
686	Dotations aux amortissements et aux provisions - Charges financières	68	FR
6866	Dotations aux provisions pour dépréciation des éléments financiers	686	FR
68662	Dotations aux provisions financières	6866	FR
68665	Valeurs mobilières de placement	6866	FR
687	Dotations aux amortissements et aux provisions - Charges exceptionnelles	68	FR
6871	Dotations aux amortissements exceptionnels des immobilisations	687	FR
6876	Dotations aux provisions pour dépréciations exceptionnelles	687	FR
689	Engagements à réaliser sur ressources affectées	68	FR
6894	Engagements à réaliser sur subventions attribuées	689	FR
6895	Engagements à réaliser sur dons manuels affectés	689	FR
6897	Engagements à réaliser sur legs et donations affectés	689	FR
69	IMPOTS SUR LES BENEFICES	6	FR
695	Impôts sur les sociétés	69	FR
7	CLASSE 7. Comptes de produits 	0	FR
70	VENTES DE PRODUITS FINIS, PRESTATIONS DE SERVICES, MARCHANDISES	7	FR
701	Ventes de produits finis	70	FR
706	Prestations de services	70	FR
707	Ventes de marchandises	70	FR
708	Produits des activités annexes	70	FR
7081	Produits des prestations fournies au personnel	708	FR
7083	Locations diverses	708	FR
7084	Mise à disposition de personnel facturée	708	FR
7088	Autres produits d'activités annexes	708	FR
709	Rabais, remises et ristournes accordés par l'association	70	FR
71	PRODUCTION STOCKEE (OU DESTOCKAGE)	7	FR
713	Variation des stocks (en-cours de production, produits)	71	FR
7133	Variation des en-cours de production de biens	713	FR
7134	Variation des en-cours de production de services	713	FR
7135	Variation des stocks de produits	713	FR
72	PRODUCTION IMMOBILISEE	7	FR
74	SUBVENTIONS D'EXPLOITATION	7	FR
75	AUTRES PRODUITS DE GESTION COURANTE	7	FR
751	Redevances pour concessions, brevets, licences, marques, procédés, droits et valeurs similaires	75	FR
754	Collectes	75	FR
756	Cotisations	75	FR
757	Quote-part d'éléments du fonds associatif virée au compte de résultat	75	FR
7571	Quote-part de subventions d'investissement (renouvelables) virée au compte de résultat	757	FR
7573	Quote-part des apports virée au compte de résultat	757	FR
758	Produits divers de gestion courante	75	FR
7585	Contributions volontaires	758	FR
7586	Contributions volontaires	758	FR
7587	Contributions volontaires	758	FR
7588	Contributions volontaires	758	FR
76	PRODUITS FINANCIERS	7	FR
761	Produits des participations	76	FR
762	Produits des autres immobilisations financières	76	FR
7621	Revenus des titres immobilisés	762	FR
7624	Revenus des prêts	762	FR
764	Revenus des valeurs mobilières de placement	76	FR
765	Escomptes obtenus	76	FR
766	Gains de change	76	FR
767	Produits nets sur cessions de valeurs mobilières de placement	76	FR
768	Autres produits financiers	76	FR
7681	Intérêts des comptes financiers débiteurs	768	FR
77	PRODUITS EXCEPTIONNELS	7	FR
771	Produits exceptionnels sur opérations de gestion	77	FR
7713	Libéralités perçues	771	FR
7714	Rentrées sur créances amorties	771	FR
7715	Subvention d'équilibre	771	FR
7717	Dégrèvements d'impôts (autres qu'impôts sur les bénéfices)	771	FR
7718	Autres produits exceptionnels sur opérations de gestion	771	FR
772	Produits sur exercices antérieurs (à reclasser)	77	FR
775	Produits des cessions d'éléments d'actif	77	FR
7751	Immobilisations incorporelles	775	FR
7752	Immobilisations corporelles	775	FR
7756	Immobilisations financières	775	FR
777	Quote-part des subventions d'investissement virée au résultat de l'exercice	77	FR
778	Autres produits exceptionnels	77	FR
78	REPRISES SUR AMORTISSEMENTS ET PROVISIONS	7	FR
781	Reprises sur amortissements et provisions (à inscrire dans les produits d'exploitation)	78	FR
7811	Reprises sur amortissements des immobilisations incorporelles et corporelles	781	FR
7815	Reprises sur provisions pour risques et charges d'exploitation	781	FR
7816	Reprises sur provisions pour dépréciation des immobilisations incorporelles et corporelles	781	FR
7817	Reprises sur provisions pour dépréciation des actifs circulants (autres que les valeurs mobilières de placement)	781	FR
786	Reprises sur provisions (à inscrire dans les produits financiers)	78	FR
7866	Reprises sur provisions pour dépréciation des éléments financiers	786	FR
78662	Immobilisations financières	7866	FR
78665	Valeurs mobilières de placement	7866	FR
787	Reprises sur provisions (à inscrire dans les produits exceptionnels)	78	FR
7876	Reprise sur provisions pour dépréciations exceptionnelles	787	FR
789	Report des ressources non utilisées des exercices antérieurs	78	FR
79	TRANSFERTS DE CHARGES	7	FR
791	Transferts de charges d'exploitation	79	FR
796	Transferts de charges financières	79	FR
797	Transferts de charges exceptionnelles	79	FR
8	CLASSE 8. CONTRIBUTIONS VOLONTAIRES 	0	FR
86	EMPLOIS DES CONTRIBUTIONS VOLONTAIRES EN NATURE - Répartition par nature de charges	8	FR
860	Secours en nature, alimentaires, vestimentaires, ...	86	FR
861	Mise à disposition gratuite de biens	86	FR
8611	Mise à disposition gratuite de locaux	861	FR
8612	Mise à disposition gratuite de matériels	861	FR
862	Prestations	86	FR
864	Personnel bénévole	86	FR
87	CONTRIBUTIONS VOLONTAIRES EN NATURE - Répartition par nature de ressources	8	FR
870	Bénévolat	87	FR
871	Prestations en nature	87	FR
875	Dons en nature	87	FR
9	Comptes analytiques	0	FR
\.

--
-- PostgreSQL database dump complete
--
