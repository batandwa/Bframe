<?php
class MathsAggregate
{
	const SUM=1;
	const COUNT=2;
	const AVERAGE=3;

	public function sum()
	{
		return array_sum(func_get_args());
	}
	public function count()
	{
		return count(func_get_args());
	}
	public function average()
	{
		throw new Exception("Not yet implemented.");
		return array_sum(func_get_args());
	}

	public static function apply($values, $aggregateType)
	{
		switch ($aggregateType)
		{
			case self::SUM:
			{
				return call_user_func("array_sum", $values);
				break;
			}
			case self::COUNT:
			{
				return call_user_func("count", $values);
				break;
			}
			case self::AVERAGE:
			{
				$sum = 0;
				foreach ($values as $val)
				{
					$sum += $val;
				}

				return $sum/count($values);
				break;
			}
		}
	}
}