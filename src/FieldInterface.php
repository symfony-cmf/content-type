<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldInterface
{
    public function getViewType();

    public function getFormType();

    public function configureOptions(OptionsResolver $options);
}