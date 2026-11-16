<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

final class Utilisateur
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT u.*, r.libelle AS role
             FROM utilisateur u JOIN role r ON r.id_role = u.id_role
             WHERE u.email = :email"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT u.*, r.libelle AS role
             FROM utilisateur u JOIN role r ON r.id_role = u.id_role
             WHERE u.id_utilisateur = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function emailExists(string $email): bool
    {
        $stmt = Database::connection()->prepare("SELECT 1 FROM utilisateur WHERE email = :e");
        $stmt->execute([':e' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public static function createClient(array $d): int
    {
        $roleId = self::roleId('client');
        $stmt = Database::connection()->prepare(
            "INSERT INTO utilisateur (id_role, nom, prenom, email, mot_de_passe_hash, telephone, actif)
             VALUES (:role, :nom, :prenom, :email, :hash, :tel, 1)"
        );
        $stmt->execute([
            ':role' => $roleId, ':nom' => $d['nom'], ':prenom' => $d['prenom'],
            ':email' => $d['email'],
            ':hash' => password_hash($d['mot_de_passe'], PASSWORD_DEFAULT),
            ':tel' => $d['telephone'] ?? null,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function createEmploye(array $d): int
    {
        $roleId = self::roleId('employe');
        $stmt = Database::connection()->prepare(
            "INSERT INTO utilisateur (id_role, nom, prenom, email, mot_de_passe_hash, actif)
             VALUES (:role, :nom, :prenom, :email, :hash, 1)"
        );
        $stmt->execute([
            ':role' => $roleId, ':nom' => $d['nom'], ':prenom' => $d['prenom'],
            ':email' => $d['email'],
            ':hash' => password_hash($d['mot_de_passe'], PASSWORD_DEFAULT),
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function updateProfil(int $id, array $d): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE utilisateur SET nom=:nom, prenom=:prenom, telephone=:tel WHERE id_utilisateur=:id"
        );
        $stmt->execute([':nom' => $d['nom'], ':prenom' => $d['prenom'], ':tel' => $d['telephone'] ?? null, ':id' => $id]);
    }

    public static function updatePassword(int $id, string $plain): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE utilisateur SET mot_de_passe_hash = :h WHERE id_utilisateur = :id"
        );
        $stmt->execute([':h' => password_hash($plain, PASSWORD_DEFAULT), ':id' => $id]);
    }

    public static function employes(): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT u.* FROM utilisateur u JOIN role r ON r.id_role = u.id_role
             WHERE r.libelle = 'employe' ORDER BY u.nom"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE utilisateur SET actif = :a WHERE id_utilisateur = :id"
        );
        $stmt->execute([':a' => $active ? 1 : 0, ':id' => $id]);
    }

    private static function roleId(string $libelle): int
    {
        $stmt = Database::connection()->prepare("SELECT id_role FROM role WHERE libelle = :l");
        $stmt->execute([':l' => $libelle]);
        return (int) $stmt->fetchColumn();
    }
}
