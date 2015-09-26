begin;
SELECT setval('public.mod_payment_mp_id_seq', 10, true);
update version set val=66;
commit;