<?php

/**
 * Formatter class
 *
 * @package
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Query;

interface Formatter
{
  public function tokenize($token);

  public function untokenize($token);

  public function formatProjections(array $projections);

  public function formatTarget(array $target);

  public function formatWhere(array $where);

  public function formatOrderBy(array $orderBy);

  public function formatLimit(array $limit);

  public function formatRange(array $range);

  public function formatPermission(array $permission);

  public function formatResource(array $resource);

  public function formatRole(array $role);

  public function btrim($text);
}
