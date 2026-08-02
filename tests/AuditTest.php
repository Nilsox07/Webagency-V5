<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\AuditProtokoll;

/**
 * Prüfprotokoll — Portal-Lastenheft §3 Regel 9 und §4 `audit_events`.
 *
 * Testfall: 55
 */
final class AuditTest extends Datenbankfall
{
    /**
     * Fall 55 — ein Audit-Eintrag laesst sich weder aendern noch loeschen.
     *
     * Geprueft wird gegen die DATENBANK, nicht gegen den Anwendungscode. Eine Absicht im
     * Code ist kein Beleg: Wer eine zweite Schreibstelle baut und die Regel vergisst, merkt
     * es nie. Die Trigger aus den Migrationen 005 und 006 sagen nein.
     */
    public function testAuditEintragLaesstSichNichtAendern(): void
    {
        $id = $this->eintragAnlegen();

        try {
            $anweisung = $this->pdo->prepare('UPDATE audit_events SET action = ? WHERE id = ?');
            $anweisung->execute(['manipuliert', $id]);
            $this->fail('Ein Audit-Eintrag liess sich ändern.');
        } catch (\PDOException $fehler) {
            $this->assertStringContainsString('nie geaendert', $fehler->getMessage());
        }

        $anweisung = $this->pdo->prepare('SELECT action FROM audit_events WHERE id = ?');
        $anweisung->execute([$id]);
        $this->assertSame('probe', $anweisung->fetchColumn());
    }

    public function testAuditEintragLaesstSichNichtLoeschen(): void
    {
        $id = $this->eintragAnlegen();

        try {
            $anweisung = $this->pdo->prepare('DELETE FROM audit_events WHERE id = ?');
            $anweisung->execute([$id]);
            $this->fail('Ein Audit-Eintrag liess sich löschen.');
        } catch (\PDOException $fehler) {
            $this->assertStringContainsString('nie geloescht', $fehler->getMessage());
        }

        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM audit_events')->fetchColumn());
    }

    /** Auch ein Rundumschlag ohne WHERE kommt nicht durch. */
    public function testAuchEinLoeschenOhneBedingungWirdAbgewiesen(): void
    {
        $this->eintragAnlegen();
        $this->eintragAnlegen();

        $this->expectException(\PDOException::class);

        $this->pdo->exec('DELETE FROM audit_events');
    }

    /** Das Protokoll schreibt alle Pflichtfelder eines Statuswechsels (§4). */
    public function testStatuswechselWirdMitAltUndNeuUndAkteurProtokolliert(): void
    {
        $admin = $this->adminAnlegen();
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');

        $id = (new AuditProtokoll($this->pdo))->schreiben(
            aktion: 'statuswechsel',
            objektart: 'projects',
            objektId: null,
            akteurBenutzerId: $admin,
            organisationId: $organisation,
            alterWert: 'briefing',
            neuerWert: 'produktion',
            grund: 'Faktenfreigabe liegt vor',
            detail: ['quelle' => 'test'],
            ip: '127.0.0.1',
        );

        $anweisung = $this->pdo->prepare('SELECT * FROM audit_events WHERE id = ?');
        $anweisung->execute([$id]);
        $eintrag = $anweisung->fetch();

        $this->assertIsArray($eintrag);
        $this->assertSame('briefing', $eintrag['old_value']);
        $this->assertSame('produktion', $eintrag['new_value']);
        $this->assertSame($admin, $eintrag['actor_user_id']);
        $this->assertSame($organisation, $eintrag['organization_id']);
        $this->assertSame('Faktenfreigabe liegt vor', $eintrag['reason']);
        $this->assertSame('127.0.0.1', $eintrag['ip']);
        $this->assertNotNull($eintrag['created_at']);
    }

    /** Das Protokoll kennt nur einen Schreibweg: INSERT. */
    public function testProtokollHatKeineAenderungsmethode(): void
    {
        $methoden = array_map(
            static fn (\ReflectionMethod $m) => $m->getName(),
            (new \ReflectionClass(AuditProtokoll::class))->getMethods(\ReflectionMethod::IS_PUBLIC),
        );

        sort($methoden);

        $this->assertSame(['__construct', 'schreiben'], $methoden);
    }

    private function eintragAnlegen(): string
    {
        return (new AuditProtokoll($this->pdo))->schreiben(
            aktion: 'probe',
            objektart: 'test',
            ip: '127.0.0.1',
        );
    }
}
