 Úlohy:
1.	Vytvorte web aplikáciu, do ktorej sa užívateľ bude môcť prihlásiť podľa svojho výberu jednou z troch možností:
•	pomocou vlastnej registrácie (kvôli tomuto bodu je potrebné sprístupniť užívateľovi registračný formulár, pri ktorom si zadá meno, priezvisko, email, login a heslo, pričom tieto údaje sa budú ukladať do databázy),
•	pomocou LDAP na STU Bratislava,
•	pomocou konta na Google.

2.	Pri vlastnej registrácii použite 2FA.

3.	Do databázy ukladajte aj informáciu o jednotlivých prihláseniach užívateľov. V tabuľke, ktorú na tento účel vytvoríte, evidujte login užívateľa, čas jeho prihlásenia a spôsob prihlásenia (registrácia, Idap, google).

4.	Po prihlásení sa do aplikácie zobrazte užívateľovi Informáciu, kto je prihlásený, vhodnú uvítaciu správu a hyperlinku (tlačidlo) "Minulé prihlásenia". Po kliknutí na tento odkaz sa užívateľovi zobrazí história prihlásení pre daný účet (registrácia, Idap, google) a štatistika, koľko užívateľov sa doteraz prihlásilo do aplikácie cez jednotlivé spôsoby prihlásenia (registrácia, Idap, google).

5.	Informácia o prihlásenom užívateľovi musí zostať stále zobrazená. Nezabudnite zabezpečiť aj odhlásenie užívateľa z aplikácie.

