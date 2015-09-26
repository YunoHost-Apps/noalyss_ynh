--
-- PostgreSQL database dump
-- Version 2008/02/10 09:19
--

--
-- Name: TABLE tmp_pcmn; Type: COMMENT; Schema:  public; Owner: phpcompta
--

COMMENT ON TABLE tmp_pcmn IS 'Plan comptable - Syndicat des copropriétaires : strict';

--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent) FROM stdin;
1	Classe 1 - Provisions, avances, subventions et emprunts	0
10	Provisions et avances :	1
102	Provisions pour travaux décidés	10
103	Avances	10
1031	Avances de trésorerie	103
1032	Avances travaux au titre de l'article 18, 6e alinéa de la loi susvisée	103
1033	Autres avances	103
12	Solde en attente sur travaux et opérations exceptionnelles	1
13	Subventions :	1
131	Subventions accordées en instance de versement	13
4	Classe 4 - Copropriétaires et tiers	0
40	Fournisseurs :	4
401	Factures parvenues	40
408	Factures non parvenues	40
409	Fournisseurs débiteurs	40
42	Personnel :	4
421	Rémunérations dues	42
43	Sécurité sociale et autres organismes sociaux :	4
431	Sécurité sociale	43
432	Autres organismes sociaux	43
44	Etat et collectivités territoriales :	4
441	Etat et autres organismes - subventions à recevoir	44
442	Etat - impôts et versements assimilés	44
443	Collectivités territoriales - aides	44
45	Collectivité des copropriétaires :	4
450	Copropriétaire individualisé	45
4501	Sur décision AG : Copropriétaire - budget prévisionnel	450
4502	Sur décision AG : Copropriétaire - travaux de l'article 14-2 de la loi susvisée et opérations exceptionnelles	450
4503	Sur décision AG : Copropriétaire - avances	450
4504	Sur décision AG : Copropriétaire - emprunts	450
459	Copropriétaire - créances douteuses	45
46	Débiteurs et créditeurs divers :	4
461	Débiteurs divers	46
462	Créditeurs divers	46
47	Compte d'attente :	4
471	Compte en attente d'imputation débiteur	47
472	Compte en attente d'imputation créditeur	47
48	Compte de régularisation :	4
486	Charges payées d'avance	48
487	Produits encaissés d'avance	48
49	Dépréciation des comptes de tiers :	4
491	Copropriétaires	49
492	Personnes autres que les copropriétaires	49
5	Classe 5 - Comptes financiers	0
50	Fonds placés :	5
501	Compte à terme	50
502	Autre compte	50
51	Banques, ou fonds disponibles en banque pour le syndicat :	5
512	Banques	51
514	Chèques postaux	51
53	Caisse.	5
6	Classe 6 - Comptes de charges	0
60	Achats de matières et fournitures :	6
601	Eau	60
602	Electricié	60
603	Chauffage, énergie et combustibles	60
604	Achats produits d'entretien et petits équipements	60
605	Matériel	60
606	Fournitures	60
61	Services extérieurs :	6
611	Nettoyage des locaux	5
612	Locations immobiliéres	61
613	Locations mobiliéres	61
614	Contrats de maintenance	61
615	Entretien et petites réparations	61
616	Primes d'assurances	61
62	Frais d'administration et honoraires :	6
621	Rémunérations du syndic sur gestion copropriété	62
6211	Rémunération du syndic	621
6212	Débours	621
6213	Frais postaux	621
622	Autres honoraires du syndic	62
6221	Honoraires travaux	622
6222	Prestations particulières	622
6223	Autres honoraires	622
623	Rémunérations de tiers intervenants	62
624	Frais du conseil syndical	62
63	Impôts - taxes et versements assimilés :	6
632	Taxe de balayage	63
633	Taxe foncière	63
634	Autres impôts et taxes	63
64	Frais de personnel :	6
641	Salaires	64
642	Charges sociales et organismes sociaux	64
643	Taxe sur les salaires	64
644	Autres (médecine du travail, mutuelles, etc.)	64
66	Charges financières des emprunts, agios ou autres :	6
661	Remboursement d'annuités d'emprunt	66
662	Autres charges financières et agios	66
67	Charges pour travaux et opérations exceptionnelles :	6
671	Travaux décidés par l'assemblée générale	67
672	Travaux urgents	67
673	Etudes techniques, diagnostic, consultation	67
677	Pertes sur créances irrécouvrables	67
678	Charges exceptionnelles	67
68	Dotations aux dépréciations sur créances douteuses.	6
7	Classe 7 - Comptes de produits	0
70	Appels de fonds :	7
701	Provisions sur opérations courantes	70
702	Provisions sur travaux de l'article 14-2 et opérations exceptionnelles	70
703	Avances	70
704	Remboursements d'annuités d'emprunts	70
71	Autres produits :	7
711	Subventions	71
712	Emprunts	71
713	Indemnités d'assurances	71
714	Produits divers (dont intérêts légaux dus par les copropriétaires)	71
716	Produits financiers	71
718	Produits exceptionnels	71
78	Reprises de dépréciations sur créances douteuses.	7
8	Comptes spéciaux	0
9	Comptes analytique	0
\.

--
-- PostgreSQL database dump complete
--
