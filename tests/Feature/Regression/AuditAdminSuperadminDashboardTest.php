<?php

namespace Tests\Feature\Regression;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Superadmin audit: the dashboard lists the five newest accounts of any role,
 * but its role switch only covered roles 1 to 3, so an instructor or an
 * institution admin was shown with an empty Role cell.
 */
class AuditAdminSuperadminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string> account name => role cell text
     */
    private function recentAccountRoleCells(string $html): array
    {
        $start = strpos($html, '<tbody');
        $end = strpos($html, '</tbody>');
        $this->assertNotFalse($start, 'The recent accounts table is gone.');
        $body = substr($html, (int) $start, (int) $end - (int) $start);

        $cells = [];

        foreach (preg_split('/<tr[^>]*>/', $body) as $row) {
            if (preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $row, $matches) !== 1 && count($matches[1]) < 2) {
                continue;
            }

            $plain = array_map(
                static fn (string $cell): string => trim(preg_replace('/\s+/', ' ', strip_tags($cell))),
                $matches[1]
            );

            if (count($plain) < 2 || $plain[0] === '') {
                continue;
            }

            $cells[$plain[0]] = $plain[1];
        }

        return $cells;
    }

    public function test_recent_accounts_table_names_every_role(): void
    {
        $institution = Institution::create([
            'name' => 'Dashboard Audit University',
            'email' => 'dashboard-audit@institution.test',
            'status' => 'active',
        ]);

        $superadmin = $this->roleUser(User::ROLE_SUPERADMIN, ['name' => 'Dashboard Superadmin']);
        $this->roleUser(User::ROLE_ADMIN, ['name' => 'Dashboard Admin']);
        $this->roleUser(User::ROLE_USER, ['name' => 'Dashboard Learner']);
        $this->roleUser(User::ROLE_INSTRUCTOR, ['name' => 'Dashboard Instructor', 'institution_id' => $institution->id]);
        $this->roleUser(User::ROLE_INSTITUTION_ADMIN, ['name' => 'Dashboard Institution Admin', 'institution_id' => $institution->id]);

        $html = $this->authenticateAs($superadmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->getContent();

        $roleCells = $this->recentAccountRoleCells($html);

        $expected = [
            'Dashboard Institution Admin' => 'Institution Admin',
            'Dashboard Instructor' => 'Instructor',
            'Dashboard Learner' => 'Student',
            'Dashboard Admin' => 'Admin',
            'Dashboard Superadmin' => 'Super Admin',
        ];

        foreach ($expected as $name => $label) {
            $key = collect(array_keys($roleCells))->first(fn (string $cell) => str_contains($cell, $name));

            $this->assertNotNull($key, "{$name} is missing from the recent accounts table.");
            $this->assertSame($label, $roleCells[$key], "The role cell for {$name} is wrong or empty.");
        }
    }
}
