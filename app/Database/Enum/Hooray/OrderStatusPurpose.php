<?php
declare(strict_types=1);

namespace App\Database\Enum\Hooray;

enum OrderStatusPurpose: string
{
    case READY_TO_CREATE_CUSTOMER_APPROVAL_PAGE = 'ready.to.create.customer.approval.page'; // Připraveno pro zpracování v queue
    case READY_FOR_CUSTOMER_APPROVAL = 'ready.for.customer.approval'; // Zveřejněno pro zákazníka
    
    case CUSTOMER_APPROVED_PREVIEW = 'customer.approved.preview'; // Zákazník schválil návrh
    case CUSTOMER_NEEDS_PREVIEW_REVISION = 'customer.needs.preview.revision'; // Zákazník požaduje úpravu
    
    case PACKAGE_SENT_TO_CUSTOMER = 'package.sent.to.customer'; // Balíček odeslán zákazníkovi
}